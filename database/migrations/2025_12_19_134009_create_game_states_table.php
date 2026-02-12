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
        Schema::create('game_states', function (Blueprint $table) {
            $table->id();
            $table->integer('current_turn_in_day')->default(0);
            $table->integer('max_turns_per_day')->default(10);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('round_started_at')->useCurrent();
            $table->integer('current_day')->default(1);
            $table->integer('max_days')->default(50);
            $table->integer('turns_per_day')->default(10);
            $table->integer('total_turns_played')->default(0); // NUEVO
            $table->integer('primordial_seal_progress')->default(0); // NUEVO (0-10)
            $table->boolean('is_victory')->default(false); // NUEVO
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_state');
    }
};
