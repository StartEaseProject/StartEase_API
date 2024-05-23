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
        Schema::create('deliberations', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('defence_id');
            $table->string('mark');
            $table->string('mention');
            $table->text('appreciation');
            $table->string('diploma_url');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('defence_id')->references('id')->on('defences')->onDelete('cascade');
            $table->primary(['member_id', 'defence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliberations');
    }
};
