<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, create a temporary column
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->string('grade_new')->nullable()->after('grade');
        });
        
        // Copy data from old column to new column
        DB::statement('UPDATE grade_submission_subject SET grade_new = CAST(grade AS CHAR)');
        
        // Drop the old column and rename the new one
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->dropColumn('grade');
            $table->renameColumn('grade_new', 'grade');
        });
    }

    public function down()
    {
        // First, create a temporary column
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->decimal('grade_old', 5, 2)->nullable()->after('grade');
        });
        
        // Copy data from new column to old column
        DB::statement('UPDATE grade_submission_subject SET grade_old = CAST(grade AS DECIMAL(5,2))');
        
        // Drop the new column and rename the old one
        Schema::table('grade_submission_subject', function (Blueprint $table) {
            $table->dropColumn('grade');
            $table->renameColumn('grade_old', 'grade');
        });
    }
}; 