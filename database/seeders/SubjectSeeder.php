<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('subjects')->insert([
        //     [
        //         'id' => 1,
        //         'order_index' => 1,
        //     ],
        //     [
        //         'id' => 2,
        //         'order_index' => 2,
        //     ],
        //     [
        //         'id' => 3,
        //         'order_index' => 3,
        //     ],
        // ]);
        Subject::factory()->count(10)->create();
    }
}
