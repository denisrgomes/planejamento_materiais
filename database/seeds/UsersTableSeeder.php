<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => str_random(10),
            'username' => 'admin',
            'password' => bcrypt('admin'),
        ]);
        
        factory(App\User::class, 50)->create();
    }
}
