<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ───────────────────────────────────────────────────────────

    /**
     * Return the Doctrine/schema type string for the `prompt` column on the
     * `report_images` table.  Works with SQLite (testing) and MySQL/PostgreSQL.
     */
    private function promptColumnType(): string
    {
        $columns = Schema::getColumns('report_images');

        foreach ($columns as $column) {
            if ($column['name'] === 'prompt') {
                return strtolower($column['type_name'] ?? $column['type'] ?? '');
            }
        }

        $this->fail('Column "prompt" not found in report_images table.');
    }

    /**
     * Instantiate the migration class under test without loading it via the
     * filesystem autoloader (the file has no class name, it returns an anonymous
     * class via `return new class …`).
     */
    private function makeMigration(): \Illuminate\Database\Migrations\Migration
    {
        return require base_path(
            'database/migrations/2026_06_13_004601_change_prompt_to_text_in_report_images_table.php'
        );
    }

    // ─── up() ────────────────────────────────────────────────────────────

    public function test_migration_up_changes_prompt_column_to_text_type()
    {
        // RefreshDatabase already ran all migrations (including this one).
        // The column should now be `text`.
        $type = $this->promptColumnType();

        $this->assertStringContainsString('text', $type);
    }

    public function test_prompt_column_accepts_strings_longer_than_255_characters_after_migration()
    {
        // RefreshDatabase ran all migrations, so `prompt` is already `text`.
        // Seed the prerequisite tables so the FK constraint is satisfied.
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $userId,
            'title' => 'Test Report',
            'subject' => 'Test Subject',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $sectionId = DB::table('report_sections')->insertGetId([
            'report_id' => $reportId,
            'title' => 'Section',
            'content' => 'Content',
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $longPrompt = str_repeat('a', 600);

        DB::table('report_images')->insert([
            'report_section_id' => $sectionId,
            'prompt' => $longPrompt,
            'image_url' => 'https://example.com/image.png',
            'source' => 'unsplash',
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stored = DB::table('report_images')
            ->where('report_section_id', $sectionId)
            ->value('prompt');

        $this->assertEquals($longPrompt, $stored);
        $this->assertEquals(600, mb_strlen($stored));
    }

    // ─── down() ──────────────────────────────────────────────────────────

    public function test_migration_down_reverts_prompt_column_to_string_type()
    {
        // First verify we start from the migrated state (text).
        $typeBefore = $this->promptColumnType();
        $this->assertStringContainsString('text', $typeBefore);

        // Run the rollback.
        $migration = $this->makeMigration();
        $migration->down();

        $typeAfter = $this->promptColumnType();

        // After down(), the column should no longer be `text` — it should be
        // `varchar` / `string`.  We assert it does NOT contain the raw "text" type.
        $this->assertStringNotContainsString('text', $typeAfter);
    }

    public function test_migration_down_then_up_restores_text_type()
    {
        $migration = $this->makeMigration();

        // Roll back first.
        $migration->down();
        $typeAfterDown = $this->promptColumnType();
        $this->assertStringNotContainsString('text', $typeAfterDown);

        // Re-apply.
        $migration->up();
        $typeAfterUp = $this->promptColumnType();
        $this->assertStringContainsString('text', $typeAfterUp);
    }
}