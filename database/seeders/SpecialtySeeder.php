<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialty::truncate();

        $csvFile = fopen(base_path("database/data/specialty.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {

                Specialty::create([
                    'spec_code' => $data['0'],
                    'spec_name' => $data['1']
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
