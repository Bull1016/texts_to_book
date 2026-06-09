<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('cover_image_url')->nullable()->change();
        });

        if (Schema::hasTable('report_images')) {
            Schema::table('report_images', function (Blueprint $table) {
                $table->text('image_url')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('cover_image_url')->nullable()->change();
        });

        if (Schema::hasTable('report_images')) {
            Schema::table('report_images', function (Blueprint $table) {
                $table->string('image_url')->nullable()->change();
            });
        }
    }
};
