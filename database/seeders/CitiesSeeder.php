<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cities;
use Illuminate\Support\Str;

class CitiesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cities::truncate();

        $csvFile = fopen(base_path("database/data/cities.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {

                Cities::create([
                    'city_id' => Str::padLeft($data['0'], 5, '0'),
                    'city_idapi' => $data['1'],
                    'city_name' => $data['2'],
                    'city_dpto' => $data['3']
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
