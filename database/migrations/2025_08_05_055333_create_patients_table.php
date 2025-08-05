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
    { {
            Schema::create('patients', function (Blueprint $table) {
                $table->id(); // id, auto-increment, primary key
                $table->string('last_name', 100);
                $table->string('first_name', 100);
                $table->date('birth_date');
                $table->enum('gender', ['M', 'F']);
                $table->string('address', 255);
                $table->string('phone', 20);
                $table->string('email', 100)->nullable();
                $table->string('emergency_contact_name', 100);
                $table->string('emergency_contact_phone', 20);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};