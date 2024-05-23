<?php

use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('photo_url')->default(env('APP_URL').User::DEFAULT_IMAGE);
            $table->boolean('is_enabled')->default(true);
            $table->string('phone_verif_code')->nullable()->default(null);
            $table->dateTime('phone_verif_code_expires_at')->nullable()->default(null);
            $table->string('tmp_phone_number')->unique()->nullable()->default(null);
            $table->string('register_verification_hash')->nullable();
            $table->timestamps();

            $table->string('person_type')->nullable();
            $table->unsignedBigInteger('person_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};