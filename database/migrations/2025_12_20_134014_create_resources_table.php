<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_type_id')->constrained('resource_types')->onDelete('cascade');
            $table->bigInteger('quantity')->default(0);
            $table->integer('production_level')->default(1);
            $table->bigInteger('total_generated')->default(0);
            $table->bigInteger('total_consumed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
