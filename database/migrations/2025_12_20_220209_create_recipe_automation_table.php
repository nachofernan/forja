<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_automation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->boolean('is_active')->default(true); // Si está activada la producción automática
            $table->integer('production_percentage')->default(100); // 10, 20, 30... 100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_automation');
    }
};