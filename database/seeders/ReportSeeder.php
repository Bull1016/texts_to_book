<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportSection;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'Why Business Fall',
            'subject' => 'Detailed analysis of business failures',
            'language' => 'fr',
            'status' => 'completed',
            'progress' => 100,
        ]);

        $section1 = ReportSection::create([
            'report_id' => $report->id,
            'title' => '1. Introduction',
            'content' => 'Introduction content here...',
            'order' => 0,
        ]);

        ReportSection::create([
            'report_id' => $report->id,
            'parent_id' => $section1->id,
            'title' => '1.1 Overview',
            'content' => 'Sub-section content here...',
            'order' => 0,
        ]);

        $section2 = ReportSection::create([
            'report_id' => $report->id,
            'title' => '2. Financial Targets',
            'content' => 'Financial content here...',
            'order' => 1,
        ]);
    }
}
