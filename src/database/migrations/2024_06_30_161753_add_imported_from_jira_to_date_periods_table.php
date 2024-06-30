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
        Schema::table('date_periods', function (Blueprint $table) {
            $table->boolean('imported_from_jira')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('date_periods', function (Blueprint $table) {
            $table->dropColumn('imported_from_jira');
        });
    }
};
