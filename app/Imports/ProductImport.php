<?php

namespace App\Imports;

use App\Models\Discount;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        $product = Product::where('title', $row[0])
            ->first();
        if ($product) {
            $product->discounts()->delete();
            $discount = floor(($row[1] - $row[2]) / 6);
            $first = $row[1] - $discount;
            $second = $first - $discount;
            $third = $second - $discount;
            $bonus = floor(floor(($third - $row[2]) / 10) / 2);
            $fbonus = $bonus;
            $sbonus = floor($fbonus / 2);
            $tbonus = floor($sbonus / 2);
            if ($row[1] < 100000) {
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
            } elseif ($row[1] >= 100000 && $row[1] <= 300000) {
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
            $product->price = $row[1];
            $product->purchased_price = $row[2];
            $product->save();
            return $product;
        }
        return;
    }

    public function startRow(): int
    {
        return 2;
    }
}
