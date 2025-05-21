<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grade_submission_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_submission_id')->constrained('grade_submissions')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('grade')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->string('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('pnph_users')->onDelete('cascade');
            $table->unique(['grade_submission_id', 'subject_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grade_submission_subject');
    }
}; 