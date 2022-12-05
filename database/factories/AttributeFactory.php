<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Attribute;
use Faker\Generator as Faker;

$factory->define(Attribute::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['خصیصه-۱', 'خصیصه-۲', 'خصیصه-۳', 'خصیصه-۴']),
        'unit' => rand(1, 100) > 50 ? $faker->randomElement(['واحد-۱', 'واحد-۲']) : null,
    ];
});
