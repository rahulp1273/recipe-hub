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
    Schema::table('recipes', function (Blueprint $table) {
        $table->integer('prep_time')->nullable()->change();
    });
}

public function down()
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->integer('prep_time')->nullable(false)->change();
    });
}
};
