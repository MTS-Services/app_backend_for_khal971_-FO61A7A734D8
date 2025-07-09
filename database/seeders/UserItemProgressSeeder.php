<?php

namespace Database\Seeders;

use App\Models\UserItemProgresss;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserItemProgressSeeder extends Seeder
{
    public function run(): void
    {
        UserItemProgresss::factory(100)->create();
    }
}
