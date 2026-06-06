<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\ReportService;
use Mockery;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_handles_exceptions_and_flashes_error()
    {
        // Manual creation since factories might be missing/broken
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->mock(ReportService::class, function ($mock) {
            $mock->shouldReceive('generateReport')->andThrow(new \Exception('Service failure'));
        });

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'title' => 'Test Report',
            'subject' => 'This is a long enough subject for validation',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Une erreur critique est survenue lors de la création du rapport.');
    }

    public function test_destroy_works_correctly()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'Test',
            'subject' => 'Subject testing long enough',
            'status' => 'completed'
        ]);

        $response = $this->actingAs($user)->delete(route('reports.destroy', $report));

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success', 'Rapport supprimé avec succès.');
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }
}
