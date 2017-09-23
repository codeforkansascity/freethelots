-- Adapted from table header info from Gregg, provided by Recorder of Deeds
--
-- For postgresql, load through psql for \connect command
create database free_the_lots;

\connect free_the_lots;

create table condominiums (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    legal_sequence numeric(5, 0) not null,
    town varchar(50),
    condo varchar(60),
    block varchar(10),
    phase varchar(10),
    building varchar(10),
    unit varchar(10),
    lot varchar(20),
    lotto varchar(20),
    lotletter varchar(10),
    remarks varchar(250),
    date_received timestamp null,
    lot_search varchar(10),
    lotto_search varchar(10),
    unit_search varchar(10),
    unitto_search varchar(10),
    unitto varchar(10)
);

create table legal_headers (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    legal_sequence numeric(5, 0) not null,
    legal_type varchar(20) not null,
    combined_legal varchar(250),
    remarks varchar(250)
);

create table lgl_freeforms (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    legal_sequence numeric(5, 0) not null,
    freeform varchar(250),
    date_received timestamp null
);

create table logi_documents (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    document_type varchar(20),
    consideration numeric(16, 2),
    index_status varchar(1),
    document_date timestamp null,
    global_id varchar(30),
    lastupdated_date timestamp null,
    image_status varchar(1),
    no_grantors numeric(5, 0),
    no_grantees numeric(5, 0),
    date_received timestamp null,
    scanned_by varchar(20),
    imgverified_by varchar(20),
    no_pages numeric(5, 0),
    scan_date timestamp null,
    imgverified_date timestamp null,
    lastupdated_by varchar(20),
    remarks varchar(250),
    conversion_status varchar(200),
    document_status varchar(50),
    nimage_status varchar(1),
    nscanned_by varchar(20),
    nscan_date timestamp null,
    nimgverified_by varchar(20),
    nimgverified_date timestamp null,
    nno_pages numeric(5, 0),
    jurisdiction varchar(4),
    class_id varchar(3),
    index_content_type varchar(10),
    image_content_type varchar(10),
    no_legals numeric(5, 0),
    document_id numeric(20, 0),
    gf_number varchar(20),
    book varchar(10),
    page varchar(10),
    book_type varchar(6),
    tax_interface_value varchar(1),
    sale_date timestamp null,
    redacted_flag varchar(1)
);

create table parties (
    name_type varchar(3) not null,
    name_seq numeric(5, 0) not null,
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    pc_flag varchar(1),
    lastname varchar(120),
    firstname varchar(70),
    relator varchar(15),
    street_number varchar(10),
    street_name varchar(100),
    suite varchar(10),
    city varchar(100),
    state varchar(2),
    zipcode varchar(15),
    zipcode_ext varchar(10),
    country varchar(30),
    area_code varchar(5),
    phone_number varchar(9),
    phone_ext varchar(5),
    email_address varchar(75),
    combined_name varchar(210),
    search_name varchar(200),
    remarks varchar(250),
    date_received timestamp null,
    address1 varchar(50),
    address2 varchar(50),
    normal_lastname varchar(200),
    normal_firstname varchar(200)
);

create table phys_documents (
    instrument_number varchar(20) not null,
    filer_no numeric(12, 0),
    date_received timestamp null,
    time_received timestamp null,
    file_page varchar(1),
    book_type varchar(6),
    book varchar(10),
    page varchar(10),
    ending_page varchar(10),
    document_quality varchar(1),
    delivery_method varchar(50),
    location varchar(10),
    backfiled varchar(1),
    remarks varchar(250),
    signaturepage numeric(5, 0),
    no_pages_recorded numeric(5, 0),
    permanent_index varchar(1),
    pcor_filed varchar(1),
    microfilm_code varchar(20),
    stamp_status numeric(2, 0),
    gf_number varchar(20),
    device_id varchar(20),
    tax_stamp_number decimal(10, 0)
);

create table returnees (
    instrument_number varchar(20) not null,
    return_seq decimal(5, 0) not null,
    return_type char(1) not null,
    lastname varchar(120),
    firstname varchar(70),
    return_date timestamp null,
    organization varchar(50),
    address1 varchar(50),
    address2 varchar(50),
    city varchar(100),
    state char(2),
    zip varchar(15),
    country varchar(30),
    phone_number varchar(20),
    email_address varchar(75),
    date_received timestamp not null
);

create table sections (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    legal_sequence numeric(5, 0) not null,
    township varchar(50),
    section varchar(10),
    range varchar(10),
    qtr1 varchar(10),
    qtr2 varchar(10),
    qtr3 varchar(10),
    qtr4 varchar(10),
    remarks varchar(250),
    date_received timestamp null,
    government_unit varchar(60)
);

create table subdivisions (
    instrument_number varchar(20) not null,
    multi_seq numeric(5, 0) not null,
    legal_sequence numeric(5, 0) not null,
    town varchar(50),
    addition varchar(60),
    lot varchar(20),
    lotto varchar(20),
    lotletter varchar(10),
    block varchar(10),
    phase varchar(10),
    remarks varchar(250),
    date_received timestamp null,
    lot_search varchar(10),
    lotto_search varchar(10)
);
