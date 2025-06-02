<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_submissions', function (Blueprint $table) {
            $table->enum('submission_type', ['regular', 'intern'])->default('regular')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('grade_submissions', function (Blueprint $table) {
            $table->dropColumn('submission_type');
        });
    }
}; 