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
        Schema::create('bank_holidays', function (Blueprint $table) {
            $table->date('date')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_holidays');
    }
};
