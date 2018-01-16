create temp table raw as (
    select *
    from raw_source
    -- Blacklist some instrument_numbers.
    where instrument_number not in (
        -- These instrument_numbers have multiple document types
        '1993I1209413',
        '1997I0019057',
        '1998I0022392',
        '1998I0023022',
        '1998I0023192',
        '1998I0023242',
        '1998I0036446',
        '1998I0061194',
        '1998I0076102',
        '1998I0076916',
        '1998I0079896',
        '1998I0083093',
        '1998I0085559',
        '1998K0037745',
        '1998K0053177',
        '1998K0056204',
        '1998K0056366',
        '1998K0059748',
        '1998K0065501',
        '1999I0016471',
        '1999I0096033',
        '1999K0047460',
        '2000K0002822',
        '2000K0013125',
        '2000K0041322',
        '2001I0017397',
        '2001K0005559',
        '2001K0072857',
        '2001K0075665',
        '2002I0004949',
        '2002I0004950',
        '2002I0018483',
        '2002I0033608',
        '2002I0084736',
        '2002K0017029',
        '2002K0031612',
        '2002K0040273'
    )
    order by combined_legal
    limit 50000
);

insert into document_type (name)
select distinct document_type from raw
on conflict do nothing;

insert into entity (name)
select distinct grantor from raw
union
select distinct grantee from raw
on conflict do nothing;

insert into transfer (instrument_number, date_received, document_date, document_type_id, files)
select raw.instrument_number,
       raw.date_received,
       -- Grab the earliest document_date - of the few cases, earlier ones are more common and have page_files more
       -- often.
       (array_agg(distinct raw.document_date order by raw.document_date))[1],
       document_type.id,
       array_agg(distinct raw.page_file) filter (where raw.page_file is not null)
from raw
inner join document_type
    on document_type.name = raw.document_type
group by instrument_number, date_received, document_date, document_type.id
on conflict
do nothing;

insert into transfer_parcel (transfer_id, parcel_id)
select raw.id, parcel_combined.id
from raw
inner join parcel_combined
    on parcel_combined.description = raw.combined_legal
on conflict
do nothing;

with raw_with_transfer as (
    -- Populating unique_strings can be done on the entity_dirty table instead to try to get common across _all_ things.
    -- Otherwise, we can try unique_strings here, but the _dirty table is nice for validating/improving unique_strings
    select transfer.id as transfer_id, array_agg(distinct raw.grantee), array_agg(distinct raw.grantor)
    from raw
    inner join transfer
        on transfer.instrument_number = raw.instrument_number
    group by transfer.id
), transfer_grantees as (
    select raw_with_transfer.transfer_id, grantees.id, party_type.id
    from raw_with_transfer
    inner join entity_dirty as grantees
        on grantees.name = raw.grantee
    inner join party_type
      on party_type.name = 'grantee'
)
), transfer_grantors as (
    select raw_with_transfer.transfer_id, grantees.id, party_type.id
    from raw_with_transfer
    inner join entity_dirty as grantors
        on grantors.name = raw.grantor 
    inner join party_type
      on party_type.name = 'grantor'
)
insert into transfer_party (transfer_id, entity_id, party_type_id)
select * from transfer_grantees
union
select * from transfer_grantors;

# TODO: Populate transfer_parcel

# TODO: Invert entity_dirty and entity dependency to match parcel_combined
# TODO: Populate entity_dirty
