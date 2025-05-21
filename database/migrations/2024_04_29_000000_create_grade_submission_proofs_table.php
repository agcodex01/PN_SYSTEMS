<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grade_submission_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_submission_id')->constrained('grade_submissions')->onDelete('cascade');
            $table->string('user_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('pnph_users')->onDelete('cascade');
            $table->unique(['grade_submission_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grade_submission_proofs');
    }
}; 