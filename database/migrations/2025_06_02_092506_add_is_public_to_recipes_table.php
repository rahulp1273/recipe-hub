<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after('instructions');
            $table->integer('likes_count')->default(0)->after('is_public');
            $table->integer('views_count')->default(0)->after('likes_count');
        });
    }

    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'likes_count', 'views_count']);
        });
    }
};
