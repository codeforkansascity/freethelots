<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // $this->call(UsersTableSeeder::class);
        factory(App\Party::class, 50)->create();
        factory(App\Parcel::class, 100)->create();
        factory(App\Transfer::class, 200)->create();

        DB::table('document_type')->insert([
            'name' => 'Quick Claim Deed'
        ]);
        DB::table('document_type')->insert([
            'name' => 'Warranty Deed'
        ]);
        DB::table('document_type')->insert([
            'name' => 'Other Deed'
        ]);

    }
}
