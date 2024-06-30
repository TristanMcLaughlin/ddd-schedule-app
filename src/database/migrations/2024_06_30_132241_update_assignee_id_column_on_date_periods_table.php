<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('date_periods', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['assignee_id']);
        });

        Schema::table('date_periods', function (Blueprint $table) {
            // Change the column type to string
            $table->string('assignee_id')->change();

            // Recreate the foreign key constraint
            $table->foreign('assignee_id')->references('id')->on('assignees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('date_periods', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['assignee_id']);
        });

        Schema::table('date_periods', function (Blueprint $table) {
            // Change the column type back to uuid
            $table->uuid('assignee_id')->change();

            // Recreate the foreign key constraint
            $table->foreign('assignee_id')->references('id')->on('assignees')->onDelete('cascade');
        });
    }
};
