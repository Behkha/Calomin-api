<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DiscountCode;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\User;

$factory->define(DiscountCode::class, function (Faker $faker) {

    return [

        'code' => Str::random(10),

        'percent' => rand(10, 50),

        'max' => $faker->randomElement([20000, 30000, 40000, 50000, 100000]),

        'expiration_date' => $faker->dateTimeBetween('now', '+30 days'),

        'user_phone' => $faker->unique()->randomElement(User::pluck('phone')),
    ];
});
