<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProjectDetails::class);
        $this->call(BlockDetails::class);
        $this->call(ApartmentDetails::class);
        $this->call(MainDescriptionDetails::class);
        $this->call(SubDescriptionDetails::class);
        $this->call(ConstructionDetails::class);
      
    }
}
