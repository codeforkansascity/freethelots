<?php

use Faker\Generator as Faker;

$factory->define(App\Parcel::class, function (Faker $faker) {
    return [
        'town' => $faker->city,
        'subdivision' => $faker->companySuffix,
        'lot' => $faker->numberBetween(1,500),
        'lotletter' => $faker->randomLetter,
        'lotto' => $faker->randomDigit,
        'block' => $faker->randomDigit,
        'remarks' => $faker->sentence()
    ];
});

/*create table parcel (
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
);*/