-- This one shows some crappy duplicated rows, my guess is from bad joins on their end (as well as multiple grantees).
-- This could be a big cause of a lot of the other issues. I _think_ the multiple grantors is alright and allowed.
select instrument_number, grantor, grantee, combined_legal, page_file
from raw_source
where instrument_number = '1998I0054620'
order by grantor, grantee, combined_legal, page_file;
