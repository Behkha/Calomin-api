<?php

use App\Models\BannersIndex;
use Illuminate\Database\Seeder;

class BannerIndicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        BannersIndex::create([
            'index' => 1,
            'max' => 2
        ]);

        BannersIndex::create([
            'index' => 2,
            'max' => 4
        ]);

        BannersIndex::create([
            'index' => 3,
            'max' => 4
        ]);

        BannersIndex::create([
            'index' => 4,
            'max' => 4
        ]);
    }
}
