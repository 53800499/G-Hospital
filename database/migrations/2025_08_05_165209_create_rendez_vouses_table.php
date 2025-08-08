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
        Schema::create('rendez_vouses', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade'); // Patient concerné
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Médecin concerné
            $table->dateTime('date_time'); // Date et heure du RDV
            $table->string('reason', 255); // Raison du RDV
            $table->enum('statut', ['confirmed', 'canceled', 'postponed'])->default('confirmed'); // Statut du RDV
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vouses');
    }
};