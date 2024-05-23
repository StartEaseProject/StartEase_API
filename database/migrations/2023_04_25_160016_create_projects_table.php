<?php

use App\Models\Project;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('trademark_name');
            $table->string('scientific_name');
            $table->timestamp('submission_date');
            $table->timestamp('updated_at')->nullable();
            $table->date('decision_date')->nullable();
            $table->date('recourse_decision_date')->nullable();
            $table->text('resume');
            $table->string('status')->default(Project::STATUSES['PENDING']);
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('project_holder_id');
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('co_supervisor_id')->nullable();
            $table->boolean('is_authorized_defence')->default(false);
            $table->json('progress')->nullable();
            $table->json('files')->nullable();
            $table->unsignedBigInteger('defence_id')->nullable();
            $table->softDeletes();

            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('project_holder_id')->references('id')->on('users');
            $table->foreign('supervisor_id')->references('id')->on('users');
            $table->foreign('co_supervisor_id')->references('id')->on('users');
            $table->foreign('defence_id')->references('id')->on('defences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};