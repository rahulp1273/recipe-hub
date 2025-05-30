<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('ingredients'); // ["2 cups flour", "1 tsp salt"]
            $table->text('instructions');
            $table->string('image')->nullable();
            $table->integer('prep_time'); // minutes
            $table->integer('cook_time'); // minutes
            $table->integer('servings');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->enum('category', ['breakfast', 'lunch', 'dinner', 'snack', 'dessert']);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
