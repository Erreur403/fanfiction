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
        Schema::create('histoire_favoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('favoris_id')->constrained('favoris')->onDelete('cascade');
            $table->foreignId('histoire_id')->constrained('histoires')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histoire_favoris');
    }
};
