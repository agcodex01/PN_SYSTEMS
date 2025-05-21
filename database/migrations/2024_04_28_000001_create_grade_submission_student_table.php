<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grade_submission_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_submission_id')->constrained('grade_submissions')->onDelete('cascade');
            $table->string('user_id');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('grade', 5, 2)->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('pn_users')->onDelete('cascade');
            $table->unique(['grade_submission_id', 'user_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grade_submission_student');
    }
}; 