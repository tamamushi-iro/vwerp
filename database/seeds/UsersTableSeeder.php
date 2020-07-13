<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $faker = \Faker\Factory::create('ro_RO');
        for($i = 0; $i < 10; $i++) {
        	User::create([
        		'name' => $faker->name,
        		'email' => $faker->unique()->safeEmail,
        		'phone' => $faker->tollFreePhoneNumber,
        		'address' => $faker->address,
        		'password' => bcrypt('Hello')
        	]);
        }
    }
}