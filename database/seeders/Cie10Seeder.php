<?php

namespace Database\Seeders;

use App\Models\Cie10;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Cie10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cie10::truncate();

        $csvFile = fopen(base_path("database/data/cie10.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 13000, ",")) !== FALSE) {
            if (!$firstline) {

                Cie10::create([
                    "cie_code" => $data[0],
                    "cie_name" => $data[1],
                    "cie_sex" => $data[2],
                    "cie_limi" => $data[3],
                    "cie_limf" => $data[4],
                    "cie_mortality" => $data[5]
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
