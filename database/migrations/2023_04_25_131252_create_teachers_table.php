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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('birth_place')->nullable();
            $table->unsignedBigInteger('establishment_id')->nullable();
            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('speciality_id')->references('id')->on('specialities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};