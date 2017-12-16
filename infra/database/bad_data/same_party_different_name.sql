-- We should be able to work on identifying duplicated, but different names. Start is the unique_strings function, but
-- would probably remove none/null like values first.
uelect distinct instrument_number, grantee
from raw_source
where instrument_number = '1998K0034879';
