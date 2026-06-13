<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change the `prompt` column on the `report_images` table to `text`.
     *
     * Updates the database schema so the `prompt` column stores long-form text instead of a 255-character string.
     */
    public function up(): void
    {
        Schema::table('report_images', function (Blueprint $table) {
            $table->text('prompt')->change();
        });
    }

    /**
     * Revert the `report_images.prompt` column to a string with length 255.
     *
     * Modifies the `report_images` table to change the `prompt` column back to `string('prompt', 255)`.
     */
    public function down(): void
    {
        Schema::table('report_images', function (Blueprint $table) {
            $table->string('prompt', 255)->change();
        });
    }
};
