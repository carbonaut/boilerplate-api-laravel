<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name'              => $faker->name,
        'last_name'               => $faker->name,
        'gender'                  => $faker->randomElement([1, 2, 9]),
        'email'                   => $faker->unique()->safeEmail,
        'email_verified_at'       => now(),
        'password'                => Hash::make($faker->password()),
        'remember_token'          => Str::random(10),
    ];
});
