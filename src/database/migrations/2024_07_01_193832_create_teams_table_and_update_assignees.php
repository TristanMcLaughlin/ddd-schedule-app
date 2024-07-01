<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->string('id')->unique()->primary(); // Use unique string as primary key
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('assignees', function (Blueprint $table) {
            $table->string('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('assignees', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::dropIfExists('teams');
    }
};
