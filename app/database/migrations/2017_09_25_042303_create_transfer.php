<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('instrument_number', 60);
            $table->integer('document_type_id');
            $table->integer('parcel_id');
            $table->integer('grantee_id');
            $table->integer('grantor_id');
            $table->date('date_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transfer');
    }
}

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