<?php

use Illuminate\Database\Seeder;

class DemandaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Demanda::class, 50)->create();
    }
}
