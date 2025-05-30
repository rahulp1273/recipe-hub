<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class RecipeController extends Controller
{
    // Show all recipes
    public function index()
    {
        $recipes = Recipe::with('user')->latest()->get();

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes
        ]);
    }

    // Show single recipe
    public function show(Recipe $recipe)
    {
        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe->load('user')
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Recipes/Create');
    }

    // Store new recipe
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'ingredients' => 'required|array',
            'instructions' => 'required|string',
            'prep_time' => 'required|integer',
            'cook_time' => 'required|integer',
            'servings' => 'required|integer',
            'difficulty' => 'required|string',
            'category' => 'required|string',
        ]);

        Recipe::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_replace(' ', '-', strtolower($request->title)),
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'instructions' => $request->instructions,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'servings' => $request->servings,
            'difficulty' => $request->difficulty,
            'category' => $request->category,
            'is_vegetarian' => $request->is_vegetarian ?? false,
        ]);

        return redirect()->route('recipes.index');
    }

    // Show edit form
    public function edit(Recipe $recipe)
    {
        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe
        ]);
    }

    // Update recipe
    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'ingredients' => 'required|array',
            'instructions' => 'required|string',
            'prep_time' => 'required|integer',
            'cook_time' => 'required|integer',
            'servings' => 'required|integer',
            'difficulty' => 'required|string',
            'category' => 'required|string',
        ]);

        $recipe->update([
            'title' => $request->title,
            'slug' => str_replace(' ', '-', strtolower($request->title)),
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'instructions' => $request->instructions,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'servings' => $request->servings,
            'difficulty' => $request->difficulty,
            'category' => $request->category,
            'is_vegetarian' => $request->is_vegetarian ?? false,
        ]);

        return redirect()->route('recipes.show', $recipe);
    }

    // Delete recipe
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index');
    }
}
