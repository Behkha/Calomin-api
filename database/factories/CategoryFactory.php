<?php

use App\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement([
            'دسته بندی-۱',
            'دسته بندی-۲',
            'دسته بندی-۳',
            'دسته بندی-۴',
        ]),
        'logo' => 'http://lorempixel.com/400/200/',
    ];
});
