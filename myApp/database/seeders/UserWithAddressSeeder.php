<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserWithAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        $total   = 1_000_000;
        $batch   = 10_000;
        $rounds  = (int) ceil($total / $batch);

        for ($i = 1; $i <= $rounds; $i++) {

            fake()->unique(true);
            
            User::factory()
                ->count($batch)
                ->create();

            $seededSoFar = $i * $batch;
            $this->command->info("Seeded {$i}×{$batch} users ({$seededSoFar}/{$total})");
        }
    }
}
