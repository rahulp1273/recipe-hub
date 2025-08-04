<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiRecipeController extends Controller
{
    /**
     * Generate a recipe using AI
     */
    public function generateRecipe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        try {
            // For now, we'll use a simple recipe generation approach
            // In production, you'd integrate with Hugging Face API
            $recipeData = $this->generateRecipeFromPrompt($validated['prompt']);

            // Create the recipe in database
                    // Generate unique slug
        $baseSlug = Str::slug($recipeData['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Check if slug exists and make it unique
        while (Recipe::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $recipe = Recipe::create([
            'user_id' => Auth::id(),
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

            return response()->json([
                'success' => true,
                'message' => 'AI Recipe generated successfully!',
                'data' => $recipe->load('user')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Recipe Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recipe. Please try again.',
                'error' => 'Service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Generate recipe data from prompt using Hugging Face AI
     */
    private function generateRecipeFromPrompt(string $prompt): array
    {
        try {
            // Get Hugging Face API key
            $apiKey = config('services.huggingface.api_key');
            
            if (!$apiKey || $apiKey === 'your_api_key_here') {
                // Fallback to templates if no API key
                return $this->getFallbackRecipe($prompt);
            }

            // Create a detailed prompt for the AI
            $aiPrompt = $this->createAIPrompt($prompt);

            // Call Hugging Face API
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
                
                // Parse AI response and convert to recipe format
                $recipeData = $this->parseAIResponse($aiResponse, $prompt);
                
                if ($recipeData) {
                    return $recipeData;
                }
            }

            // Fallback to templates if AI fails
            return $this->getFallbackRecipe($prompt);

        } catch (\Exception $e) {
            \Log::error('AI Recipe Generation Error: ' . $e->getMessage());
            try {
                return $this->getFallbackRecipe($prompt);
            } catch (\Exception $fallbackError) {
                \Log::error('Fallback Recipe Error: ' . $fallbackError->getMessage());
                // Return a basic recipe if everything fails
                return [
                    'title' => ucfirst($prompt) . ' Recipe',
                    'description' => 'A delicious recipe created for you.',
                    'category' => 'dinner',
                    'prep_time' => 15,
                    'cook_time' => 20,
                    'servings' => 4,
                    'ingredients' => [
                        '2 tablespoons olive oil',
                        '1 onion, chopped',
                        '2 cloves garlic, minced',
                        'Salt and pepper to taste',
                        'Fresh herbs for garnish'
                    ],
                    'instructions' => [
                        'Heat oil in a large pan over medium heat',
                        'Add onions and cook until softened',
                        'Add garlic and cook for 1 minute',
                        'Add your main ingredients',
                        'Season with salt and pepper',
                        'Cook until everything is heated through',
                        'Serve hot and enjoy!'
                    ]
                ];
            }
        }
    }

    /**
     * Create a detailed prompt for AI recipe generation
     */
    private function createAIPrompt(string $userPrompt): string
    {
        return "You are a professional chef. Create a detailed recipe for: {$userPrompt}. 
        
        Please provide:
        - Recipe title
        - Brief description
        - Category (breakfast, lunch, dinner, dessert, snack)
        - Prep time in minutes
        - Cook time in minutes
        - Number of servings
        - List of ingredients with measurements
        - Step-by-step cooking instructions
        
        Format the response as a natural recipe description that I can parse into structured data.";
    }

    /**
     * Parse AI response and convert to recipe format
     */
    private function parseAIResponse(array $aiResponse, string $originalPrompt): ?array
    {
        try {
            $text = $aiResponse[0]['generated_text'] ?? '';
            
            if (empty($text)) {
                return null;
            }

            // Extract recipe information from AI response
            $recipeData = $this->extractRecipeData($text, $originalPrompt);
            
            return $recipeData;

        } catch (\Exception $e) {
            \Log::error('AI Response Parsing Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract recipe data from AI text response
     */
    private function extractRecipeData(string $text, string $originalPrompt): array
    {
        // Default values
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

        // Try to extract more specific data from AI response
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Extract title
            if (empty($recipeData['title']) && !empty($line) && !str_contains($line, 'ingredients') && !str_contains($line, 'instructions')) {
                $recipeData['title'] = $line;
            }
            
            // Extract times
            if (str_contains(strtolower($line), 'prep') && preg_match('/(\d+)/', $line, $matches)) {
                $recipeData['prep_time'] = (int)$matches[1];
            }
            
            if (str_contains(strtolower($line), 'cook') && preg_match('/(\d+)/', $line, $matches)) {
                $recipeData['cook_time'] = (int)$matches[1];
            }
            
            // Extract servings
            if (str_contains(strtolower($line), 'serving') && preg_match('/(\d+)/', $line, $matches)) {
                $recipeData['servings'] = (int)$matches[1];
            }
        }

        return $recipeData;
    }

    /**
     * Extract ingredients from AI text
     */
    private function extractIngredients(string $text): array
    {
        $ingredients = [];
        $lines = explode("\n", $text);
        $inIngredients = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (str_contains(strtolower($line), 'ingredients')) {
                $inIngredients = true;
                continue;
            }
            
            if (str_contains(strtolower($line), 'instructions') || str_contains(strtolower($line), 'directions')) {
                break;
            }
            
            if ($inIngredients && !empty($line) && !str_contains($line, ':')) {
                // Clean up the ingredient line
                $ingredient = trim($line, "•-* ");
                if (!empty($ingredient)) {
                    $ingredients[] = $ingredient;
                }
            }
        }
        
        // If no ingredients found, provide defaults based on prompt
        if (empty($ingredients)) {
            $ingredients = $this->getDefaultIngredients($text);
        }
        
        return array_slice($ingredients, 0, 10); // Limit to 10 ingredients
    }

    /**
     * Extract instructions from AI text
     */
    private function extractInstructions(string $text): array
    {
        $instructions = [];
        $lines = explode("\n", $text);
        $inInstructions = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (str_contains(strtolower($line), 'instructions') || str_contains(strtolower($line), 'directions')) {
                $inInstructions = true;
                continue;
            }
            
            if ($inInstructions && !empty($line) && !str_contains($line, ':')) {
                // Clean up the instruction line
                $instruction = trim($line, "•-*1234567890. ");
                if (!empty($instruction)) {
                    $instructions[] = $instruction;
                }
            }
        }
        
        // If no instructions found, provide defaults
        if (empty($instructions)) {
            $instructions = $this->getDefaultInstructions($text);
        }
        
        return array_slice($instructions, 0, 8); // Limit to 8 instructions
    }

    /**
     * Generate title from prompt
     */
    private function generateTitle(string $prompt): string
    {
        $prompt = trim($prompt);
        return ucfirst($prompt) . ' Recipe';
    }

    /**
     * Determine category from prompt
     */
    private function determineCategory(string $prompt): string
    {
        $promptLower = strtolower($prompt);
        
        if (str_contains($promptLower, 'breakfast') || str_contains($promptLower, 'pancake') || str_contains($promptLower, 'omelet')) {
            return 'breakfast';
        } elseif (str_contains($promptLower, 'dessert') || str_contains($promptLower, 'cake') || str_contains($promptLower, 'cookie')) {
            return 'dessert';
        } elseif (str_contains($promptLower, 'salad') || str_contains($promptLower, 'vegetable')) {
            return 'lunch';
        } else {
            return 'dinner';
        }
    }

    /**
     * Get default ingredients based on prompt
     */
    private function getDefaultIngredients(string $text): array
    {
        $promptLower = strtolower($text);
        
        if (str_contains($promptLower, 'chicken')) {
            return [
                '2 chicken breasts',
                '2 tablespoons olive oil',
                '1 onion, chopped',
                '2 cloves garlic, minced',
                'Salt and pepper to taste',
                '1 cup chicken broth'
            ];
        } elseif (str_contains($promptLower, 'pasta')) {
            return [
                '1 pound pasta',
                '2 tablespoons olive oil',
                '3 cloves garlic, minced',
                '1/2 cup parmesan cheese',
                'Salt and pepper to taste',
                'Fresh herbs for garnish'
            ];
        } else {
            return [
                '2 tablespoons olive oil',
                '1 onion, chopped',
                '2 cloves garlic, minced',
                'Salt and pepper to taste',
                'Fresh herbs for garnish'
            ];
        }
    }

    /**
     * Get default instructions based on prompt
     */
    private function getDefaultInstructions(string $text): array
    {
        $promptLower = strtolower($text);
        
        if (str_contains($promptLower, 'chicken')) {
            return [
                'Season chicken with salt and pepper',
                'Heat oil in a large pan over medium heat',
                'Cook chicken until golden brown on both sides',
                'Add onions and garlic, cook until softened',
                'Add broth and simmer until chicken is cooked through',
                'Serve hot with your favorite sides'
            ];
        } elseif (str_contains($promptLower, 'pasta')) {
            return [
                'Bring a large pot of salted water to boil',
                'Cook pasta according to package directions',
                'Heat oil in a large skillet over medium heat',
                'Add garlic and cook until fragrant',
                'Add cooked pasta to the skillet',
                'Toss with parmesan cheese and season to taste',
                'Garnish with fresh herbs and serve'
            ];
        } else {
            return [
                'Heat oil in a large pan over medium heat',
                'Add onions and cook until softened',
                'Add garlic and cook for 1 minute',
                'Add your main ingredients',
                'Season with salt and pepper',
                'Cook until everything is heated through',
                'Serve hot and enjoy!'
            ];
        }
    }

    /**
     * Fallback recipe templates when AI is not available
     */
    private function getFallbackRecipe(string $prompt): array
    {
        $promptLower = strtolower($prompt);
        
        if (str_contains($promptLower, 'chicken') || str_contains($promptLower, 'curry')) {
            return [
                'title' => 'Simple Chicken Curry',
                'description' => 'A delicious and easy chicken curry recipe.',
                'category' => 'dinner',
                'prep_time' => 15,
                'cook_time' => 25,
                'servings' => 4,
                'ingredients' => [
                    '2 chicken breasts, cubed',
                    '1 onion, chopped',
                    '2 cloves garlic, minced',
                    '1 inch ginger, grated',
                    '2 tablespoons curry powder',
                    '1 can coconut milk',
                    'Salt and pepper to taste',
                    'Fresh cilantro for garnish'
                ],
                'instructions' => [
                    'Heat oil in a large pan over medium heat',
                    'Add chicken and cook until browned',
                    'Add onions, garlic, and ginger, cook until softened',
                    'Add curry powder and cook for 1 minute',
                    'Pour in coconut milk and simmer for 15 minutes',
                    'Season with salt and pepper',
                    'Garnish with fresh cilantro and serve with rice'
                ]
            ];
        } elseif (str_contains($promptLower, 'pasta') || str_contains($promptLower, 'noodle')) {
            return [
                'title' => 'Quick Garlic Pasta',
                'description' => 'A simple and delicious garlic pasta recipe.',
                'category' => 'dinner',
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 4,
                'ingredients' => [
                    '1 pound spaghetti',
                    '4 tablespoons olive oil',
                    '4 cloves garlic, minced',
                    '1/2 cup parmesan cheese',
                    'Salt and pepper to taste',
                    'Fresh parsley for garnish'
                ],
                'instructions' => [
                    'Cook pasta according to package directions',
                    'Heat oil in a large skillet over medium heat',
                    'Add garlic and cook until fragrant',
                    'Add cooked pasta to the skillet',
                    'Toss with parmesan cheese',
                    'Season with salt and pepper',
                    'Garnish with fresh parsley and serve'
                ]
            ];
        } else {
            return [
                'title' => 'Simple Stir Fry',
                'description' => 'A quick and healthy stir fry recipe.',
                'category' => 'dinner',
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 4,
                'ingredients' => [
                    '2 tablespoons vegetable oil',
                    '1 onion, sliced',
                    '2 cloves garlic, minced',
                    'Mixed vegetables',
                    'Soy sauce to taste',
                    'Salt and pepper to taste'
                ],
                'instructions' => [
                    'Heat oil in a wok or large skillet over high heat',
                    'Add onions and cook until softened',
                    'Add garlic and cook for 30 seconds',
                    'Add vegetables and stir fry for 5 minutes',
                    'Add soy sauce and season to taste',
                    'Serve hot with rice or noodles'
                ]
            ];
        }
    }

    /**
     * Get AI generation history for user
     */
    public function getAiGeneratedRecipes(): JsonResponse
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->where('is_ai_generated', true)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }
} 