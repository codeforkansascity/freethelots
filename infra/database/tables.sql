-- Adapted from table header info from Gregg, provided by Recorder of Deeds. Move to models/migrations soon.
--
-- Intended for postgresql
begin;

create table entity (
    id serial primary key,
    name varchar not null unique
);

create index on entity(lower(name) varchar_pattern_ops);

-- This table tracks entity names as they were recorded in the raw data. It is largely intended as a stop gap. Until the
-- name normalization logic is more refined, we'll rely on this to match/show exact names in the UI. Ideally, if the
-- name deduplication/normalization logic can be refined enough, this can be removed. The logic will try to turn things
-- like "Cable Co" and "Cable Company" into the same entity.
create table entity_dirty (
    id serial primary key,
    entity_id int not null references entity,
    name varchar not null unique
);

create index on entity_dirty(lower(name) varchar_pattern_ops);

-- This table tracks entity names as they were recorded in the raw data. It is largely intended as a stop gap. Until the
-- name normalization logic is more refined, we'll rely on this to match/show exact names in the UI. Ideally, if the
-- name deduplication/normalization logic can be refined enough, this can be removed. The logic will try to turn things
-- like "Cable Co" and "Cable Company" into the same entity.
create table parcel (
    id serial primary key,
    city varchar(50) not null,
    subdivision varchar(60) not null,
    block varchar(20) not null,
    -- TODO: Need to see if I can get more specific regex
    lot varchar(100) not null,
    frontage varchar(12) not null,
    -- TODO: This should handle nulls in the future.
    unique (city, subdivision, block, lot, frontage)
);
create index on parcel(lower(block) varchar_pattern_ops);
create index on parcel(lower(city) varchar_pattern_ops);
create index on parcel(lower(frontage) varchar_pattern_ops);
create index on parcel(lower(lot) varchar_pattern_ops);
create index on parcel(lower(subdivision) varchar_pattern_ops);

-- This table is referenced by a transfer to ensure we don't lose track of the actual combined_legal since parsing is
-- not foolproof. Ex: if we parsed two legals as the same, but they were different properties, we want a way to verify
-- (both to QA/improve parsing and for Gregg in the UI). The logic will probably treat two transfers as related if the
-- parcel_id is the same, but we still need to report the proper combined in case they are not. Perhaps flag the
-- chain of transfers when the legals are not all the exact same.
create table parcel_combined (
    id serial primary key,
    -- Allow null parcel_id for unparsable descriptions
    parcel_id int references parcel,
    description varchar not null unique
);
-- Really only needed for the first join to populate the transfer table
create index on parcel_combined(description varchar_pattern_ops);

create table document_type (
    id serial primary key,
    name varchar(20) not null unique
);

create table party_type (
    id int primary key,
    name varchar(7) not null unique
);

insert into party_type (id, name)
values (1, 'grantor'), (2, 'grantee');

-- This is the main joining table. It represents a transfer of a property from party(s) to party(s). 
create table transfer (
    id serial primary key,
    instrument_number varchar(13) not null unique,
    -- TODO: This should be updated to point to "parcel" once/if we're more confident in the parsing.
    parcel_id int not null references parcel_combined,
    date_received timestamp not null
);

create table transfer_party (
    transfer_id int not null references transfer (id),
    -- TODO: This should be updated to point to "entity" once we've trust the normalization logic
    entity_id int not null references entity_dirty (id),
    party_type_id int not null references party_type (id),
    -- I reckon the same party could be a grantor and grantee in case of multiple grantors granting to a subset
    unique (transfer_id, entity_id, party_type_id)
);

-- Need this because there _may_ "valid" cases where the same instrument_number has multiple document types?
-- TODO: Confirm ^
create table document (
    transfer_id int not null unique references transfer (id),
    type_id int not null references document_type not null,
    date timestamp not null,
    files varchar(100)[]
);

commit;
