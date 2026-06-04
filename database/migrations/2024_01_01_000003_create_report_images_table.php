<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_section_id')->constrained('report_sections')->onDelete('cascade');
            $table->string('prompt');
            $table->string('image_url');
            $table->string('source')->default('unsplash');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_images');
    }
};
