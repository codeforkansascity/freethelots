-- This is the only 5 char instrument number. All void/none ones
select * from raw_source where instrument_number in ('1994K');
-- This is the only 10 char instrument number. Empty combined legal
select * from raw_source where instrument_number in ('1996K43649');
-- These are the only 13 char instrument numbers. All Land Trust ones.
select * from raw_source where instrument_number in ('1994I1295896I', '1995K11810540', '1995K11810541', '1995K11818051');
