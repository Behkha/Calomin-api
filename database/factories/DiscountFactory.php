<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Discount;
use Faker\Generator as Faker;

$factory->define(Discount::class, function (Faker $faker) {
    return [
        'product_id' => App\Models\Product::inRandomOrder()->first()->id,
        'discount_percent' => rand(10, 50),
        'expiration_date' => $faker->dateTimeBetween('now', '+30 days'),
    ];
});
