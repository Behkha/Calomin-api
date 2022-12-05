<?php

namespace App\Imports;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;

class TestImport implements ToModel
{
    public function model(array $row)
    {
        if (!$row[0] || !is_integer($row[3])) {
            return;
        }
        $category = Category::where('name', $row[4])
            ->first();
        if (!$category) {
            $category = Category::create(['name' => $row[4]]);
            $category->attributes()->save(new Attribute(['name' => 'خصیصه']));
            $category->attributes()->save(new Attribute(['name' => 'خصیصه']));
        }
        $product = Product::create([
            'title' => $row[1],
            'description' => $row[2],
            'price' => $row[3],
            'purchased_price' => (int) $row[3] - 5000,
            'category_id' => $category->id,
            'brand_id' => 1,
            'gallery' => ['products/pic.png'],
            'quantity' => 100,
            'counter' => 24,
            'counter_created_at' => now()->addHours(24),
            'bonus' => rand(100, 1000),
        ]);
        DB::table('product_features')
            ->insert(['title' => 'ویژگی', 'product_id' => $product->id]);
        DB::table('attribute_product')
            ->insert(['product_id' => $product->id, 'attribute_id' => Attribute::inRandomOrder()->first()->id, 'value' => 'تست']);
        $discount = floor(($row[3] - 5000) / 6);
        $first = $row[3] - $discount;
        $second = $first - $discount;
        $third = $second - $discount;
        $bonus = floor(floor(($third - 5000) / 10) / 2);
        $fbonus = $bonus;
        $sbonus = floor($fbonus / 2);
        $tbonus = floor($sbonus / 2);
        if ($row[3] < 100000) {
            Discount::create([
                'product_id' => $product->id,
                'from' => 1,
                'to' => 24,
                'price' => $first,
                'bonus' => $fbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 25,
                'to' => 50,
                'price' => $second,
                'bonus' => $sbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 51,
                'to' => 100,
                'price' => $third,
                'bonus' => $tbonus,
            ]);
        } elseif ($row[3] >= 100000 && $row[3] <= 300000) {
            Discount::create([
                'product_id' => $product->id,
                'from' => 1,
                'to' => 5,
                'price' => $first,
                'bonus' => $fbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 6,
                'to' => 25,
                'price' => $second,
                'bonus' => $sbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 26,
                'to' => 100,
                'price' => $third,
                'bonus' => $tbonus,
            ]);
        } else {
            Discount::create([
                'product_id' => $product->id,
                'from' => 1,
                'to' => 2,
                'price' => $first,
                'bonus' => $fbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 3,
                'to' => 5,
                'price' => $second,
                'bonus' => $sbonus,
            ]);
            Discount::create([
                'product_id' => $product->id,
                'from' => 6,
                'to' => 10,
                'price' => $third,
                'bonus' => $tbonus,
            ]);
        }
        return $product;
    }
}
