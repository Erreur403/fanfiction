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
        Schema::create('histoires', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('resume');
            $table->string('couverture');
            $table->enum('statut', ['Publier', 'Brouillon']);
            $table->enum('progression', ['EnCours', 'Inachever', 'Terminer']);
            $table->integer('restriction_age')->nullable();
            $table->string('mot_cles');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histoires');
    }
};
