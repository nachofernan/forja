<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Fuego", "Hierro", "Mithril", etc.
            $table->integer('tier'); // 0, 1, 2, 3
            $table->text('description')->nullable();
            $table->decimal('passive_generation_base', 10, 2)->default(0); // Solo Tier 0
            $table->string('icon')->nullable(); // Emoji o path
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_types');
    }
};