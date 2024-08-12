<?php

namespace Database\Seeders;

use App\Models\AffiliationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AffiliationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AffiliationType::truncate();

        $csvFile = fopen(base_path("database/data/affiliation_type.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {

                AffiliationType::create([
                    'affi_code' => $data['0'],
                    'affi_name' => $data['1']
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
