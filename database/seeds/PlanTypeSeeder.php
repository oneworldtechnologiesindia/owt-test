<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planTypes = [
            'BASIC' => [
                'silver' => 5, // silver level
                'gold' => 4, // gold level
                'platinum' => 3, // platinum level
                'diamond' => 2.5, //diamond level
            ],
            'PREMIUM' => [
                'silver' => 5, // silver level
                'gold' => 4, // gold level
                'platinum' => 3, // platinum level
                'diamond' => 2.5, //diamond level
            ],
            'SUPREME' => [
                'silver' => 5, // silver level
                'gold' => 4, // gold level
                'platinum' => 3, // platinum level
                'diamond' => 2.5, //diamond level
            ]
        ];

        foreach ($planTypes as $planType => $levels) {
            DB::table('plan_types')->insert([
                'plan_type' => $planType,
                'silver_level' => $levels['silver'],
                'gold_level' => $levels['gold'],
                'platinum_level' => $levels['platinum'],
                'diamond_level' => $levels['diamond'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $planTypesForDistributer = [
            'BASIC' => [
                '0' => 25, // silver level
                '1' => 20, // gold level
                '2' => 15, // platinum level
                '3' => 10, //diamond level
            ],
            'PREMIUM' => [
                '0' => 25, // silver level
                '1' => 20, // gold level
                '2' => 15, // platinum level
                '3' => 10, //diamond level
            ],
            'SUPREME' => [
                '0' => 25, // silver level
                '1' => 20, // gold level
                '2' => 15, // platinum level
                '3' => 10, //diamond level
            ]
        ];

        foreach ($planTypesForDistributer as $planType => $levels) {
            DB::table('plan_types')->insert([
                'plan_type' => $planType,
                'silver_level' => $levels['0'],
                'gold_level' => $levels['1'],
                'platinum_level' => $levels['2'],
                'diamond_level' => $levels['3'],
                'type' => 'DISTRIBUTER',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
