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
        Schema::create('jurys', function (Blueprint $table) {
            $table->unsignedBigInteger('jury_id');
            $table->unsignedBigInteger('defence_id');
            $table->string('role');
            $table->timestamps();
            $table->primary(['jury_id', 'defence_id']);
            $table->foreign('jury_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('defence_id')->references('id')->on('defences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurys');
    }
};
