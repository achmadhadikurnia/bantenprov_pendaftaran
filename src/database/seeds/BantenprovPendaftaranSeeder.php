<?php

use Illuminate\Database\Seeder;

class BantenprovPendaftaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(BantenprovPendaftaranSeederPendaftaran::class);
    }
}
