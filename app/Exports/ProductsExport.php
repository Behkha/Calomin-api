<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
{
    public function collection()
    {
        $products = Product::all();
        $data = [];
        array_push($data, [
            'عنوان',
            'قیمت خرید',
            'قیمت فروش',
            'تعداد',
            'توضیحات',
            'دسته بندی-1',
            'دسته بندی-2',
            'دسته بندی-3',
            'برند',
            'ایران کد',
        ]);
        foreach ($products as $product) {
            $cat1 = Category::find($product->category_id);
            if ($cat1->parent_id) {
                $cat2 = Category::find($cat1->parent_id);
                if ($cat2->parent_id) {
                    $cat3 = Category::find($cat2->parent_id);
                }
            }
            array_push($data, [
                $product->title,
                $product->purchased_price,
                $product->price,
                $product->quantity,
                $product->description,
                $cat1->name,
                isset($cat2) ? $cat2->name : null,
                isset($cat3) ? $cat3->name : null,
                Brand::find($product->brand_id)->title,
                $product->code,
            ]);
        }
        return collect($data);
    }
}
