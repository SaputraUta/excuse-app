<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            1 => 'PMS',
            2 => 'POS',
            3 => 'Developer',
            4 => 'MC',
            5 => 'Report',
        ];

        foreach ($divisions as $id => $name) {
            Division::updateOrCreate(
                ['id' => $id],
                ['name' => $name]
            );
        }
    }
}
