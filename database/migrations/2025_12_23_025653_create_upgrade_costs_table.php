<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upgrade_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upgrade_id')->constrained('upgrades')->onDelete('cascade');
            $table->foreignId('resource_type_id')->constrained('resource_types')->onDelete('cascade');
            $table->integer('quantity_required');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upgrade_costs');
    }
};