<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $statuses = ['pending', 'generating', 'completed', 'failed'];

        foreach (range(1, 20) as $i) {
            Report::create([
                'user_id' => $user->id,
                'title' => "Report $i",
                'subject' => "Subject for report $i",
                'language' => 'en',
                'status' => $statuses[array_rand($statuses)],
                'progress' => 0,
            ]);
        }
    }
}
