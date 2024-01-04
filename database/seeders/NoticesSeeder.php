<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the notices table with dummy data
        Notice::create([
            'title' => 'Important Notice 1',
            'content' => 'This is the content of the first notice.',
        ]);

        // Add more notices as needed
    }
}
