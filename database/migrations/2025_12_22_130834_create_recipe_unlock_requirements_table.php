<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_unlock_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('resource_type_id')->constrained('resource_types')->onDelete('cascade');
            $table->integer('quantity_required');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_unlock_requirements');
    }
};