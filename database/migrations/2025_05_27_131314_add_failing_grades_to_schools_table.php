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
        // (Removed failing_grade_min and failing_grade_max columns, as they already exist.)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // (No down migration needed.)
    }
};
