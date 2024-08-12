<?php

namespace Database\Seeders;

use App\Models\Cups;
use Illuminate\Database\Seeder;

class CupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cups::truncate();

        $csvFile = fopen(base_path("database/data/cups.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 6000, ",")) !== FALSE) {
            if (!$firstline) {

                Cups::create([
                    'cups_code' => $data['0'],
                    'cups_name' => $data['1'],
                    'cups_description' => $data['2'],
                    'cups_sex' => $data['3'],
                    'cups_years_start' => $data['4'],
                    'cups_years_end' => $data['5'],
                    'cups_iss' => $data['6'],
                    'cups_soat' => $data['7'],
                    'cups_particular' => $data['8'],
                    'cups_other' => $data['7'],
                    'cups_file' => $data['10'],
                    'cups_ot_type' => ($data['12'] === '') ? 0 : $data['12'],
                    'cups_state' => $data['11']
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
