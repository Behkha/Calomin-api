<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $qty = rand(50, 200);
    $purchasedPrice = rand(10000, 99999);
    $price = $purchasedPrice + rand(10000, 30000);
    $counter = rand(1, 96);
    return [
        'title' => $faker->randomElement([
            'محصول-۱',
            'محصول-۲',
            'محصول-۳',
            'محصول-۴',
            'محصول-۵',
        ]),
        'gallery' => ['http://lorempixel.com/400/200/', 'http://lorempixel.com/400/200/'],
        'price' => $price,
        'purchased_price' => $purchasedPrice,
        'quantity' => $qty,
        'category_id' => Category::inRandomOrder()->first()->id,
        'description' => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.',
        'counter_created_at' => now()->addHours($counter),
        'counter_sales' => rand($qty - 40, $qty),
        'counter' => $counter,
        'bonus' => rand(50, 200),
        'brand_id' => Brand::inRandomOrder()->first()->id,
        'code' => rand(1000, 9999) . rand(1000, 9999),
        'has_tax' => rand(1, 10) > 5 ? true : false,
    ];
});
