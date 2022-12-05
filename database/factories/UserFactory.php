<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => '0915' . $faker->unique()->randomNumber(7),
        'password' => 'Alireza1',
        'is_verified' => true,
        'registered_at' => now()->toDateString(),
    ];
});
