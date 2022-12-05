<?php

use Illuminate\Database\Seeder;
use App\Models\DeliveryMethod;

class DeliveryMethodSeeder extends Seeder
{
    public function run()
    {
        DeliveryMethod::create([
            'name' => '‍‍‍پست پیشتاز',
            'description' => '۱ روز',
            'price' => 10000
        ]);

        DeliveryMethod::create([
            'name' => '‍‍‍پست معمولی',
            'description' => '۲ روز',
            'price' => 5000
        ]);
    }
}
