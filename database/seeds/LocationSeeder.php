<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = json_decode(file_get_contents(storage_path() . '/Province.json'));
        foreach ($json as $item) {
            $state = App\Models\State::create(['name' => $item->name]);
            foreach ($item->Cities as $city) {
                App\Models\City::create(['name' => $city->name, 'state_id' => $state->id]);
            }
        }
    }
}
