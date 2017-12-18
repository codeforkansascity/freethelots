insert into document_type (name)
select distinct document_type from raw_source
on conflict do nothing;

insert into entity (name)
select distinct grantor from raw_source
union
select distinct grantee from raw_source
on conflict do nothing;

insert into transfer (instrument_number, parcel_id, grantee_id, grantor_id, date_received)
select instrument_number, parcel.id, grantees.id, grantors.id, date_received
from raw_source
inner join parcel_combined
    on parcels.description = raw_source.combined_legal
on conflict
do nothing;

with raw_with_transfer as (
    select transfer.id as transfer_id, unique_strings(raw_source.grantee), unique_strings(raw_source.grantor)
    from raw_source
    inner join transfer
        on transfer.instrument_number = raw_source.instrument_number
    group by transfer.id
), transfer_grantees as (
    select transfer_id, grantees.id, party_type.id
    from raw_with_transfer
    inner join entity as grantees
        on grantees.name = raw_source.grantee
    inner join party_type
      on party_type.name = 'grantee'
)
), transfer_grantors as (
    select transfer_id, grantees.id, party_type.id
    from raw_with_transfer
    inner join entity as grantors
        on grantors.name = raw_source.grantor 
    inner join party_type
      on party_type.name = 'grantor'
)
insert into transfer_party (transfer_id, entity_id, party_type_id)
select * from transfer_grantees
union
select * from transfer_grantors;


insert into document (instrument_number, type_id, date, files)
select instrument_number, document_type.id, document_date
from raw_source
;
