create extension if not exists pg_trgm;

create or replace function unique_strings_sfunc(encountered varchar[], new varchar)
returns varchar[]
as $$
    with similarities as (
        select encountered, 1 as preference, similarity(encountered, $2)
        from unnest($1) as encountered
        union
        select $2 as match, 2 as preference, 1 as similarity
    ), qualifying as (
        select encountered as match
        from similarities
        where round(similarity::numeric, 2) >= 0.65
        order by preference, similarity
        limit 1
    ), unioned as (
        select encountered
        from similarities s
        left join qualifying q
          on q.match = s.encountered
        where q.match is null
          and s.preference = 1
        union
        select case when length($2) > length(match)
                   then $2
                   else match
               end as encountered
        from qualifying
    )
    select array_agg(encountered)
    from unioned
    where encountered is not null;
$$
language sql
immutable parallel safe;

drop aggregate if exists unique_strings (varchar);
create aggregate unique_strings (varchar) (
    sfunc = unique_strings_sfunc,
    stype = varchar[]
);

/* create temp table if not exists test_data (name) as ( */
/*     values ('CADLE CO II INC'), ('CADLE COMPANY II INC'), ('COFFELT DAVID E'), ('SARGENT TRESSA A') */
/* ); */

/* select unique_strings(name) from (select name from test_data order by name) as name; */
