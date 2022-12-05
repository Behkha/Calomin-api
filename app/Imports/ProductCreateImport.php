<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductCreateImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {

        $category = Category::where('name', trim($row[5]))->first();
        $brand = Brand::where('title', trim($row[6]))->first();
        if (!$category) {
            $category = Category::create([
                'name' => $row[5]
            ]);
        }

        if (!$brand) {
            $brand = Brand::create([
                'title' => $row[6]
            ]);
        }
        $product = Product::updateOrCreate([
            'code' => $row[7]
        ],[
            'title' => $row[0],
            'purchased_price' => $row[1],
            'price' => $row[2],
            'quantity' => $row[3],
            'description' => $row[4],
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'has_tax' => $row[8] == '1',
            'counter' => $row[9],
            'counter_created_at' => now()->addHours($row[9]),
            'counter_sales' => 0,
            'mass' => $row[11],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $images = [];
        for ($i = 1; $i <= $row[10]; $i++) {
            array_push($images, 'products/' . $row[7] . '_' . $i . '.jpg');
        }
        if (isset($row[10]) && $row[10] > 0) {
            $product->gallery = $images;
        } else {
            $product-> gallery = [];
        }

        $discount = floor(($row[2] - $row[1]) / 6);
        $first = $row[2] - $discount;
        $second = $first - $discount;
        $third = $second - $discount;
        $bonus = floor(floor(($row[2] - $third) / 10) / 2);
        $fbonus = $bonus;
        $sbonus = floor($fbonus / 2);
        $tbonus = floor($sbonus / 2);

        $product->bonus = $bonus;
        $product->save();
        Discount::where('product_id', $product->id)->delete();
        if ($row[2] < 100000) {
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
        } elseif ($row[2] >= 100000 && $row[2] <= 300000) {
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
        return;
    }

    public function startRow(): int
    {
        return 2;
    }
}
