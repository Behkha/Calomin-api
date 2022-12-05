<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AttributeSet;
use Faker\Generator as Faker;

$factory->define(AttributeSet::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
