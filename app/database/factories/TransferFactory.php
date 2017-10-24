<?php

use Faker\Generator as Faker;

$factory->define(App\Transfer::class, function (Faker $faker) {
    $r = rand(1,50);
    $e = rand(1,50);
    //while($e == $r) $e = rand(1,50);
    return [
        'instrument_number' => bcrypt(rand(1,100)),
        'document_type_id' => rand(1,3),
        'parcel_id' => rand(1,100) ,
        'grantee_id' => $e,
        'grantor_id' => $r,
        'date_received' => $faker->dateTimeThisDecade(),
        ];
});

/**
 * create table transfer (
id serial primary key,
instrument_number varchar(20) not null unique,
document_type_id int references document_type not null,
parcel_id int references parcel not null,
grantee_id int references party not null,
grantor_id int references party not null,
date_received timestamp not null,
check (grantee != grantor)
);
 */