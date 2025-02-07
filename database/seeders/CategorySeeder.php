<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nom' => 'Action'],
            ['nom' => 'Aventure'],
            ['nom' => 'Science-fiction'],
            ['nom' => 'Fantaisie'],
            ['nom' => 'Horreur'],
        ];

        DB::table('categories')->insert($categories);
    }
}
