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
            $table->string('language')->default('fr')->after('subject');
        });

        Schema::table('report_sections', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('report_id')->constrained('report_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_sections', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
