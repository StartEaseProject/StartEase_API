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
        Schema::create('defences', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('establishment_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('other_place')->nullable();
            $table->string('mode');
            $table->string('nature');
            $table->text('reserves')->nullable();
            $table->json('files')->nullable();
            $table->string('guest')->nullable();
            $table->timestamps();

            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defences');
    }
};
