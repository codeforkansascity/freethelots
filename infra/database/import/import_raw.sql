-- psql -v ON_ERROR_STOP=1 -h <> -U postgres -d <> -f import.sql
\timing on
select 'Creating and populating "raw_source"';
begin;
drop table if exists raw_source;
create table raw_source (
    id serial primary key,
    instrument_number varchar(15), -- Most are 13 long
    book varchar(10),
    combined_legal varchar(500),
    date_received timestamp,
    document_date timestamp,
    document_type varchar(20),
    grantee varchar(120),
    grantor varchar(120),
    page varchar(10),
    page_file varchar(100) check (page_file != '')
);
-- Since the data uses single and double quotes for inches/feet and has no quoting, must set alternate quoting to
-- something that won't appear.
\copy raw_source (instrument_number, book, page, date_received, document_date, document_type, grantee, grantor, combined_legal, page_file) from 'deeds.clean.csv' with (delimiter '|', encoding 'utf8', format csv, freeze true, header true, quote E'\b');
commit;

select 'Normalizing empty values';
update raw_source
set
    combined_legal = coalesce(
        nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(
        nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(
        nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(
        nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(
        nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(nullif(
        nullif(nullif(
            regexp_replace(
                trim(regexp_replace(combined_legal, '^\s*SUBDIVISION;', '')),
                '\s+',
                ' ',
                'g'
            ),
            'VOID'), 'UNKNOWN'), 'UNKNOWN CODES'), 'SEE INSTR'), 'NOT GIVEN'), 'NONE GIVEN'), 'NO LEGAL'),
            'NO LEGAL PAGE'), 'NO LEGAL LETTER'), 'NO LEGAL GIVEN'), 'NO LEGAL DESCR'), 'NO LEGAL ATTACHED'),
            'NA'), 'N/A'), 'N'), 'LEGAL NOT DESIGNATED'), 'LEGAL MISSPELLED'), 'INCORRECT REFERENCE TO LEGAL'),
            'INCORRECT REFERENCE TO LEGAL LETTER'), 'INCORRECT REF TO LEGAL'), 'INCORRECT REF TO LEGAL LETTER'),
            'FREEFORM; NOT DESIGNATED'), 'FREEFORM; NONE'), 'FREEFORM; LEGAL NOT DESIGNATED'), 'FREEFORM'), '-'),
            '*'), ''), ''''), '`'), '='), ','), ';'), '?'), '/'), '//'), '.'), '''N'), 'NAS'), 'NJONE'), 'NN'),
            'NNN'), 'NNONE'), 'NOE'), 'NOEN'), 'NON'), 'NONR'), 'NOONE'), 'NS'), 'X'), 'N / A'),'NONEE'), 'NONEEE'),
            'NO SUB'), 'NO DOC'), 'BNONE'), 'NONE'),
        ''
    ),
    document_type = regexp_replace(coalesce(
        nullif(trim(document_type), 'NONE'),
        ''
    ), '\s+', ' ', 'g'),
    grantee = regexp_replace(coalesce(
        nullif(nullif(nullif(nullif(nullif(nullif(trim(grantee), ''), 'VOID'), 'UNKNOWN'), 'NOT GIVEN'), 'NONE GIVEN'), 'NONE'),
        ''
    ), '\s+', ' ', 'g'),
    grantor = regexp_replace(coalesce(
        nullif(nullif(nullif(nullif(nullif(nullif(trim(grantee), ''), 'VOID'), 'UNKNOWN'), 'NOT GIVEN'), 'NONE GIVEN'), 'NONE'),
        ''
    ), '\s+', ' ', 'g')
;

select 'Creating indexes';
create index raw_source_book_idx on raw_source using btree (book);
create index raw_source_combined_legal_idx on raw_source using btree (combined_legal);
create index raw_source_date_received_document_date_idx on raw_source using btree (date_received, document_date);
create index raw_source_document_type_idx on raw_source using btree (document_type);
create index raw_source_grantee_idx on raw_source using btree (grantee);
create index raw_source_grantor_idx on raw_source using btree (grantor);
create index raw_source_instrument_number_idx on raw_source using btree (instrument_number);
create index raw_source_lower_idx on raw_source using btree (lower((grantor)::text) varchar_pattern_ops);
create index raw_source_lower_idx1 on raw_source using btree (lower((grantee)::text) varchar_pattern_ops);
create index raw_source_page_file_idx on raw_source using btree (page_file);
create index raw_source_page_idx on raw_source using btree (page);
