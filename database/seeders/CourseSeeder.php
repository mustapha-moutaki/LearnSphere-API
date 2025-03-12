<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Course::create([
            'title' => 'Web Development 101',
            'description' => 'Learn basic web development concepts.',
            'category_id' => 1,
        ]);
    }
}
