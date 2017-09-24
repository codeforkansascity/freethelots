-- Adapted from table header info from Gregg, provided by Recorder of Deeds. Move to models/migrations soon.
--
-- Intended for postgresql

create table party (
    id serial primary key,
    first_name varchar(70),
    last_name varchar(120) not null check (last_name != ''),
    unique (first_name, last_name)
);

create table parcel (
    id serial primary key,
    town varchar(50),
    subdivision varchar(60),
    lot varchar(20),
    lotto varchar(20),
    lotletter varchar(10),
    block varchar(10),
    remarks varchar(250),
    -- This needs to handle null values in the future
    unique (town, subdivision, lot, lotto, lotletter, block, remarks)
);

create table document_type (
    id serial primary key,
    name varchar(20)
);

create table transfer (
    id serial primary key,
    instrument_number varchar(20) not null unique,
    document_type_id int references document_type not null,
    parcel_id int references parcel not null,
    grantee_id int references party not null,
    grantor_id int references party not null,
    date_received timestamp not null,
    check (grantee != grantor)
);
