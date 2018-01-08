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

\timing on
select 'Normalizing empty values';
-- It's much more readable to split the parsing into two parts, though a bit slower.
update raw_source
set
    combined_legal = trim(regexp_replace(regexp_replace(combined_legal, '^\s*SUBDIVISION;', ''), '\s+', ' ', 'g')),
    document_type = trim(regexp_replace(document_type, '\s+', ' ', 'g')),
    grantee = trim(regexp_replace(grantee, '\s+', ' ', 'g')),
    grantor = trim(regexp_replace(grantor, '\s+', ' ', 'g'))
;
update raw_source
set
    combined_legal = case when combined_legal in (
        '''', '''N', '*', ',', '-', '.', '/', '//', ';', '=', '?', 'BNONE',
        'FREEFORM', 'FREEFORM; LEGAL NOT DESIGNATED', 'FREEFORM; NONE',
        'FREEFORM; NOT DESIGNATED', 'INCORRECT REF TO LEGAL LETTER',
        'INCORRECT REF TO LEGAL', 'INCORRECT REFERENCE TO LEGAL LETTER',
        'INCORRECT REFERENCE TO LEGAL', 'LEGAL MISSPELLED',
        'LEGAL NOT DESIGNATED', 'N / A', 'N', 'N/A', 'NA', 'NAS', 'NJONE',
        'NN', 'NNN', 'NNONE', 'NO DOC', 'NO LEGAL ATTACHED', 'NO LEGAL DESCR',
        'NO LEGAL GIVEN', 'NO LEGAL LETTER', 'NO LEGAL PAGE', 'NO LEGAL',
        'NO SUB', 'NOE', 'NOEN', 'NON', 'NONE GIVEN', 'NONE', 'NONEE', 'NONEEE',
        'NONR', 'NOONE', 'NOT GIVEN', 'NS', 'SEE INSTR', 'UNKNOWN CODES',
        'UNKNOWN', 'VOID', 'VOIDED', 'X', '`'
        ) then ''
        else combined_legal
    end,
    document_type = case when document_type in ('NONE') then ''
                    else document_type
    end,
    grantee = case when grantee in ('VOID', 'UNKNOWN', 'NOT GIVEN', 'NONE GIVEN', 'NONE') then ''
              else grantee
    end,
    grantor = case when grantor in ('VOID', 'UNKNOWN', 'NOT GIVEN', 'NONE GIVEN', 'NONE') then ''
              else grantor
    end
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
