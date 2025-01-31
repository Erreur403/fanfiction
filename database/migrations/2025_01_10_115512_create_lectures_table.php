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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bibliotheque_id')->constrained('bibliotheques')->onDelete('cascade');
            $table->foreignId('histoire_id')->constrained('histoires')->onDelete('cascade');
            $table->foreignId('chapitre_id')->constrained('chapitres')->onDelete('cascade');
            $table->integer('position_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
