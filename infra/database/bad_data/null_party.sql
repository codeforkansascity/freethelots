select instrument_number
from raw_source
where grantee in ('none', 'NONE', 'void', 'VOID')
   or grantee is null
   or grantor in ('none', 'NONE', 'void', 'VOID')
   or grantor is null;
