<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ReportSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($email)
    {
        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_user_cannot_view_others_report()
    {
        $user1 = $this->createUser('user1@example.com');
        $user2 = $this->createUser('user2@example.com');

        $report = Report::create([
            'user_id' => $user1->id,
            'title' => 'Private Report',
            'subject' => 'Secret subject',
            'status' => 'completed'
        ]);

        $this->actingAs($user2)
            ->get(route('reports.show', $report))
            ->assertStatus(403);
    }

    public function test_user_cannot_delete_others_report()
    {
        $user1 = $this->createUser('user1@example.com');
        $user2 = $this->createUser('user2@example.com');

        $report = Report::create([
            'user_id' => $user1->id,
            'title' => 'Private Report',
            'subject' => 'Secret subject',
            'status' => 'completed'
        ]);

        $this->actingAs($user2)
            ->delete(route('reports.destroy', $report))
            ->assertStatus(403);

        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    public function test_user_cannot_download_others_report()
    {
        $user1 = $this->createUser('user1@example.com');
        $user2 = $this->createUser('user2@example.com');

        $report = Report::create([
            'user_id' => $user1->id,
            'title' => 'Private Report',
            'subject' => 'Secret subject',
            'status' => 'completed',
            'pdf_path' => 'dummy/path.pdf'
        ]);

        $this->actingAs($user2)
            ->get(route('reports.download', $report))
            ->assertStatus(403);
    }
}
