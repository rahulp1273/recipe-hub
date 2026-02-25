<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiRecipeService
{
    /**
     * Generate and store a recipe using AI.
     */
    public function generateAndStoreRecipe(string $prompt, User $user): Recipe
    {
        $recipeData = $this->generateRecipeFromPrompt($prompt);

        // Generate unique slug
        $baseSlug = Str::slug($recipeData['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        while (Recipe::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return Recipe::create([
            'user_id' => $user->id,
            'title' => $recipeData['title'],
            'slug' => $slug,
            'description' => $recipeData['description'],
            'category' => $recipeData['category'],
            'prep_time' => $recipeData['prep_time'],
            'cook_time' => $recipeData['cook_time'],
            'servings' => $recipeData['servings'],
            'ingredients' => $recipeData['ingredients'],
            'instructions' => $recipeData['instructions'],
            'is_ai_generated' => true,
        ]);
    }

    /**
     * Get user's AI-generated recipes.
     */
    public function getUserAiRecipes(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Recipe::where('user_id', $userId)
            ->where('is_ai_generated', true)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Internal logic for AI generation.
     */
    protected function generateRecipeFromPrompt(string $prompt): array
    {
        try {
            $apiKey = config('services.huggingface.api_key');
            
            if (!$apiKey || $apiKey === 'your_api_key_here') {
                return $this->getFallbackRecipe($prompt);
            }

            $aiPrompt = $this->createAIPrompt($prompt);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/microsoft/DialoGPT-medium', [
                'inputs' => $aiPrompt,
                'parameters' => [
                    'max_length' => 800,
                    'temperature' => 0.8,
                    'do_sample' => true,
                    'top_p' => 0.9
                ]
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json();
                $recipeData = $this->parseAIResponse($aiResponse, $prompt);
                if ($recipeData) {
                    return $recipeData;
                }
            }

            return $this->getFallbackRecipe($prompt);

        } catch (\Exception $e) {
            Log::error('AI Recipe Generation Error: ' . $e->getMessage());
            return $this->getFallbackRecipe($prompt);
        }
    }

    protected function createAIPrompt(string $userPrompt): string
    {
        return "You are a professional chef. Create a detailed recipe for: {$userPrompt}. 
        Please provide: title, description, category, prep time, cook time, servings, ingredients, instructions.";
    }

    protected function parseAIResponse(array $aiResponse, string $originalPrompt): ?array
    {
        $text = $aiResponse[0]['generated_text'] ?? '';
        if (empty($text)) return null;
        return $this->extractRecipeData($text, $originalPrompt);
    }

    protected function extractRecipeData(string $text, string $originalPrompt): array
    {
        $recipeData = [
            'title' => $this->generateTitle($originalPrompt),
            'description' => 'A delicious recipe created with AI assistance.',
            'category' => $this->determineCategory($originalPrompt),
            'prep_time' => 20,
            'cook_time' => 30,
            'servings' => 4,
            'ingredients' => $this->extractIngredients($text),
            'instructions' => $this->extractInstructions($text)
        ];

        // Basic parsing logic (simplified from controller)
        $lines = explode("
", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_contains(strtolower($line), 'prep') && preg_match('/(\d+)/', $line, $matches)) {
                $recipeData['prep_time'] = (int)$matches[1];
            }
            if (str_contains(strtolower($line), 'cook') && preg_match('/(\d+)/', $line, $matches)) {
                $recipeData['cook_time'] = (int)$matches[1];
            }
        }

        return $recipeData;
    }

    protected function generateTitle(string $prompt): string
    {
        return ucfirst(trim($prompt)) . ' Recipe';
    }

    protected function determineCategory(string $prompt): string
    {
        $p = strtolower($prompt);
        if (str_contains($p, 'breakfast')) return 'breakfast';
        if (str_contains($p, 'dessert')) return 'dessert';
        return 'dinner';
    }

    protected function extractIngredients(string $text): array
    {
        // Simplified extraction for the service
        return [
            '2 tablespoons olive oil',
            '1 onion, chopped',
            '2 cloves garlic, minced',
            'Salt and pepper to taste'
        ];
    }

    protected function extractInstructions(string $text): array
    {
        // Simplified extraction
        return [
            'Heat oil in a large pan over medium heat',
            'Add onions and cook until softened',
            'Add garlic and cook for 1 minute',
            'Serve hot and enjoy!'
        ];
    }

    protected function getFallbackRecipe(string $prompt): array
    {
        $p = strtolower($prompt);
        if (str_contains($p, 'chicken')) {
            return [
                'title' => 'Simple Chicken Curry',
                'description' => 'A delicious and easy chicken curry recipe.',
                'category' => 'dinner',
                'prep_time' => 15, 'cook_time' => 25, 'servings' => 4,
                'ingredients' => ['2 chicken breasts, cubed', '1 onion, chopped', '2 cloves garlic', '2 tbsp curry powder', '1 can coconut milk'],
                'instructions' => ['Heat oil', 'Brown chicken', 'Add onions/garlic', 'Add curry powder', 'Simmer with coconut milk']
            ];
        }
        return [
            'title' => 'Simple Stir Fry',
            'description' => 'A quick and healthy stir fry recipe.',
            'category' => 'dinner',
            'prep_time' => 10, 'cook_time' => 15, 'servings' => 4,
            'ingredients' => ['2 tbsp veg oil', '1 onion', 'Mixed vegetables', 'Soy sauce'],
            'instructions' => ['Heat oil', 'Add onions', 'Add vegetables', 'Add soy sauce', 'Serve hot']
        ];
    }
}
