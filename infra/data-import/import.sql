\timing on
begin;
truncate raw_source restart identity;
-- Since the data uses single and double quotes for inches/feet and has no quoting, must set alternate quoting to
-- something that won't appear.
\copy raw_source (instrument_number, book, page, date_received, document_date, document_type, grantee, grantor, combined_legal, page_file) from 'deeds.clean.csv' with (delimiter '|', encoding 'utf8', format csv, freeze true, header true, quote E'\b');
commit;
