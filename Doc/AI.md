# 🤖 AI Recipe Generator - Implementation Guide

## 📋 Overview

The AI Recipe Generator is a feature that allows authenticated RecipeHub users to generate complete recipes using artificial intelligence. Users describe what they want to cook, and the system creates a full recipe with ingredients, instructions, cooking times, and serving information.

## 🎯 Features Implemented

### ✅ Core Functionality
- **Text-based recipe generation** from user prompts (Hugging Face when configured, otherwise smart templates)
- **Quick prompt suggestions** for common recipe types
- **Real-time generation** with loading states
- **Recipe storage** in database with AI flag
- **Recipe history** showing all AI-generated recipes
- **User authentication** required for access

### ✅ User Experience
- **Beautiful UI** with gradient design
- **Responsive layout** for all devices
- **Loading animations** during generation
- **Success/error feedback** for user actions
- **Quick navigation** to view generated recipes
- **Integration** with existing dashboard

## 🏗️ Technical Architecture

### Backend (Laravel)
```
app/Http/Controllers/Api/AiRecipeController.php  # Main AI controller
app/Models/Recipe.php                            # Updated with AI flag + slug
database/migrations/*_add_is_ai_generated.php    # Database migration
routes/api.php                                   # API routes
config/services.php                              # AI service config (Hugging Face)
```

### Frontend (Vue.js)
```
resources/js/components/AiRecipeGenerator.vue  # Main AI component
resources/js/Pages/Dashboard.vue               # Dashboard integration
```

## 📊 Database Schema

### Recipes Table Updates
```sql
ALTER TABLE recipes ADD COLUMN is_ai_generated BOOLEAN DEFAULT FALSE;
```

### New Fields
- `is_ai_generated` (boolean) - Flags AI-generated recipes

## 🔌 API Endpoints

### Generate Recipe
```http
POST /api/ai/generate-recipe
Authorization: Bearer {token}
Content-Type: application/json

{
  "prompt": "chocolate cake recipe"
}
```

**Response:**
```json
{
  "success": true,
  "message": "AI Recipe generated successfully!",
  "data": {
    "id": 1,
    "title": "Classic Chocolate Cake",
    "description": "A delicious homemade chocolate cake...",
    "ingredients": ["2 cups flour", "2 cups sugar", ...],
    "instructions": ["Preheat oven to 350°F", ...],
    "prep_time": 20,
    "cook_time": 35,
    "servings": 8,
    "is_ai_generated": true,
    "user": {...}
  }
}
```

### Get AI Generated Recipes
```http
GET /api/ai/generated-recipes
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [...],
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 5
  }
}
```

## 🎨 Frontend Components

### AiRecipeGenerator.vue
**Location:** `resources/js/components/AiRecipeGenerator.vue`

**Key Features:**
- Text input for recipe prompts
- Quick suggestion buttons
- Loading states with spinners
- Success/error message handling
- Recipe history display
- Navigation to view recipes

**Props:** None (self-contained component)

**Events:**
- Recipe generation
- Navigation to recipe view
- Error handling

## 🔧 Implementation Details

### 1. Controller Logic (AiRecipeController.php)

High‑level flow in `generateRecipe()`:

1. Validate input prompt (`required|string|max:500`).
2. Call `generateRecipeFromPrompt($prompt)`:
   - If `HUGGINGFACE_API_KEY` is configured:
     - Build a detailed natural‑language prompt.
     - Call the Hugging Face inference API.
     - Parse and extract structured recipe data (title, description, category, timings, ingredients, instructions).
   - If the API call fails or no key is present:
     - Fall back to curated templates based on prompt keywords (chicken, pasta, etc.).
3. Generate a **unique slug** for the recipe.
4. Create a `Recipe` with `is_ai_generated = true` and all structured fields.
5. Return the recipe (with `user` relation) in the JSON response.

### 2. Recipe Templates (Fallback Implementation)

When Hugging Face is not available, the controller uses a set of curated templates:

- **Chicken/Curry** → “Simple Chicken Curry”
- **Pasta/Noodles** → “Quick Garlic Pasta”
- **Other prompts** → “Simple Stir Fry”

Each template provides:

- Title & description
- Category (usually `dinner`)
- `prep_time`, `cook_time`, `servings`
- `ingredients[]` and `instructions[]`

### 3. Frontend Integration

**Dashboard Integration:**
```vue
<!-- In Dashboard.vue -->
<template>
  <div class="dashboard">
    <!-- AI Recipe Generator Section -->
    <div class="mb-12">
      <AiRecipeGenerator />
    </div>
    
    <!-- Other dashboard content -->
  </div>
</template>

<script setup>
import AiRecipeGenerator from "@/components/AiRecipeGenerator.vue";
</script>
```

## 🚀 Setup Instructions

### 1. Install Dependencies
```bash
# Backend dependencies (already installed)
composer require guzzlehttp/guzzle

# Frontend dependencies (already installed)
npm install
```

### 2. Database Migration
```bash
php artisan migrate
```

### 3. Configuration
Add to `.env` file (optional, for real AI integration):
```env
HUGGINGFACE_API_KEY=your_api_key_here
```

### 4. Start Servers
```bash
# Backend server
php artisan serve

# Frontend server
npm run dev
```

## 🎯 User Flow

### 1. Access AI Generator
- User logs in to RecipeHub
- Navigates to Dashboard
- Sees AI Recipe Generator section

### 2. Generate Recipe
- User enters recipe description (e.g., "chocolate cake")
- Clicks "Generate Recipe" button
- System shows loading animation
- Recipe is generated and saved to database
- Success message appears with recipe details

### 3. View Generated Recipe
- User clicks "View Recipe" button
- Navigates to full recipe page
- Can edit, share, or save to collections

### 4. Manage AI Recipes
- View history of AI-generated recipes
- Access all AI recipes from dashboard
- Filter recipes by AI generation

## 🔮 Future Enhancements

### 1. Real AI Integration
- **Hugging Face API** - Free tier (30K requests/month)
- **OpenAI GPT-3.5** - Low cost ($0.02 per recipe)
- **Local Models** - Ollama, LM Studio

### 2. Advanced Features
- **Recipe customization** options
- **Dietary restrictions** filtering
- **Cuisine-specific** generation
- **Ingredient substitution** suggestions
- **Cooking time optimization**

### 3. User Experience
- **Recipe rating** system
- **AI recipe categories**
- **Bulk generation** options
- **Recipe sharing** improvements
- **Mobile app** integration

## 🛠️ Troubleshooting

### Common Issues

**1. Recipe Generation Fails**
- Check user authentication
- Verify API endpoint is accessible
- Check server logs for errors

**2. Component Not Loading**
- Verify component import path
- Check Vue.js compilation
- Clear browser cache

**3. Database Errors**
- Run migrations: `php artisan migrate`
- Check database connection
- Verify table structure

### Debug Commands
```bash
# Check routes
php artisan route:list | grep ai

# Test API endpoint
curl -X POST http://localhost:8000/api/ai/generate-recipe \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"prompt": "test recipe"}'

# Check database
php artisan tinker
>>> App\Models\Recipe::where('is_ai_generated', true)->count()
```

## 📈 Performance Considerations

### Current Implementation
- **Template-based** - Fast response times
- **No external API calls** - Reliable
- **Simple keyword matching** - Efficient

### Future AI Integration
- **API rate limiting** - Respect service limits
- **Caching** - Store common requests
- **Async processing** - For complex generations
- **Fallback templates** - When AI fails

## 🔒 Security & Privacy

### Authentication
- **Sanctum bearer tokens** (`auth_token`) required for all AI endpoints
- **User ownership** - Users can only access their recipes
- **Input validation** - Sanitize user prompts

### Data Protection
- **Recipe ownership** - Clear user attribution
- **No personal data** in AI prompts
- **Secure API calls** - HTTPS only

## 📝 Code Examples

### Adding New Recipe Template
```php
// In AiRecipeController.php
private function generateRecipeFromPrompt(string $prompt): array
{
    $templates = [
        'new_recipe' => [
            'title' => 'Your Recipe Title',
            'description' => 'Recipe description...',
            'category' => 'category_name',
            'prep_time' => 15,
            'cook_time' => 30,
            'servings' => 4,
            'ingredients' => ['ingredient 1', 'ingredient 2'],
            'instructions' => ['step 1', 'step 2']
        ]
    ];
    
    // Add keyword matching logic
    if (str_contains(strtolower($prompt), 'your_keyword')) {
        return $templates['new_recipe'];
    }
}
```

### Customizing UI
```vue
<!-- In AiRecipeGenerator.vue -->
<template>
  <div class="ai-recipe-generator">
    <!-- Custom styling -->
    <div class="custom-header">
      <h3>Your Custom Title</h3>
    </div>
    
    <!-- Custom prompt suggestions -->
    <div class="custom-prompts">
      <button @click="prompt = 'custom prompt'">
        Custom Prompt
      </button>
    </div>
  </div>
</template>
```

## 🎉 Success Metrics

### User Engagement
- **Recipe generation count** per user
- **Time spent** in AI generator
- **Recipe view rate** after generation
- **User retention** after using AI feature

### Technical Metrics
- **API response times** (target: <2 seconds)
- **Generation success rate** (target: >95%)
- **Error rate** (target: <5%)
- **Database performance** (query optimization)

---

**Last Updated:** February 25, 2026  
**Version:** 1.1.0  
**Status:** ✅ Production Ready