There are numerous bad combined_legals, but a lot are in the form of plain text, mostly omitting the `CITY`/`SBD`/etc
sections and the semicolons. Ex:

`BLUE SPRINGS COUNTRY CLUB ANNEX 02-2206 IN LOTS 9 - 9`

We may still be able to parse those if we used what we know for the legals that did parse. For example, we could check
that it begins with a city identified in the `parcel.city` column and assign that, then strip that part and check agains
the rest for a subdivision. Finally, `IN LOTS 9 - 9` would be a pretty simple regex fix.
