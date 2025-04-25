<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->decimal('passing_grade_min', 3, 1)->after('terms');
            $table->decimal('passing_grade_max', 3, 1)->after('passing_grade_min');
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['passing_grade_min', 'passing_grade_max']);
        });
    }
};
