<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upgrades', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // "fuego_gen_boost_1"
            $table->string('name'); // "Llama Eterna I"
            $table->text('description')->nullable();
            $table->string('category'); // "passive_generation", "turns", "days", "efficiency"
            $table->string('effect_type'); // "multiply_generation", "add_turns", "reduce_cost"
            $table->foreignId('target_resource_type_id')->nullable()->constrained('resource_types')->onDelete('cascade');
            $table->integer('target_tier')->nullable(); // Para upgrades que afectan un tier completo
            $table->decimal('effect_value', 10, 2); // 1.5, 5, 10, etc
            $table->boolean('is_purchased')->default(false);
            $table->foreignId('requires_upgrade_id')->nullable()->constrained('upgrades')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upgrades');
    }
};