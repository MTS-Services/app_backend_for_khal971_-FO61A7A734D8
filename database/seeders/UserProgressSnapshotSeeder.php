<?php

namespace Database\Seeders;

use App\Models\UserProgressSnapshot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProgressSnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProgressSnapshot::factory(100)->create();
    }
}
