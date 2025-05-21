<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->decimal('grade', 5, 2)->nullable()->after('subject_id');
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending')->after('grade');
            $table->string('user_id')->after('status');
            
            $table->foreign('user_id')->references('user_id')->on('pnph_users')->onDelete('cascade');
            $table->unique(['grade_submission_id', 'subject_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['grade_submission_id', 'subject_id', 'user_id']);
            $table->dropColumn(['grade', 'status', 'user_id']);
        });
    }
}; 