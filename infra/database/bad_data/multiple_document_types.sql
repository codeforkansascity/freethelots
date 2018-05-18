select instrument_number, array_agg(distinct document_type)
from raw_source
group by instrument_number
having count(distinct document_type) > 1;
/*
 instrument_number |  array_agg
-------------------+--------------
 1993I1209413      | {NOT,WD}
 1997I0019057      | {AGRE,DT}
 1998I0022392      | {MISC,REL}
 1998I0023022      | {MISC,MWAIV}
 1998I0023192      | {MISC,REL}
 1998I0023242      | {DEED,TD}
 1998I0036446      | {DT,REL}
 1998I0061194      | {MWAIV,WAV}
 1998I0076102      | {ASDT,ASSN}
 1998I0076916      | {ASDT,ASSN}
 1998I0079896      | {ASDT,ASSN}
 1998I0083093      | {PREL,REL}
 1998I0085559      | {ASDT,ASSN}
 1998K0037745      | {MWAIV,WAV}
 1998K0053177      | {NOT,REQT}
 1998K0056204      | {ASDT,MISC}
 1998K0056366      | {DT,MOD}
 1998K0059748      | {FC,TD}
 1998K0065501      | {ASDT,WD}
 1999I0016471      | {ASDT,DT}
 1999I0096033      | {REL,WD}
 1999K0047460      | {ASDT,REL}
 2000K0002822      | {CAD,DEED}
 2000K0013125      | {REL,WD}
 2000K0041322      | {STL,STLR}
 2001I0017397      | {DT,REL}
 2001K0005559      | {REL,WD}
 2001K0072857      | {AGRE,DT}
 2001K0075665      | {ASDT,REL}
 2002I0004949      | {DT,WD}
 2002I0004950      | {DT,WD}
 2002I0018483      | {REL,WD}
 2002I0033608      | {DT,WD}
 2002I0084736      | {QCD,WD}
 2002K0017029      | {DT,PRD}
 2002K0031612      | {DT,FC}
 2002K0040273      | {QCD,WD}
*/
