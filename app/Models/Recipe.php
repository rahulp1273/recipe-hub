<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'category',
        'prep_time',
        'cook_time',
        'servings',
        'ingredients',
        'instructions',
        'views',
        'rating',
        'is_public',
        'likes_count',
        'views_count',
        'image',
        'is_ai_generated'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
        'views' => 'integer',
        'rating' => 'decimal:1',
        'is_public' => 'boolean',
        'is_ai_generated' => 'boolean',
    ];

    // Auto-generate slug when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = Str::slug($recipe->title);
                // Make sure slug is unique
                $originalSlug = $recipe->slug;
                $count = 1;
                while (static::where('slug', $recipe->slug)->exists()) {
                    $recipe->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(RecipeLike::class);
    }

    public function views()
    {
        return $this->hasMany(RecipeView::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function collections()
    {
        return $this->belongsToMany(RecipeCollection::class, 'collection_recipes', 'recipe_id', 'collection_id')
                    ->withTimestamps();
    }

    public function getAverageRatingAttribute()
    {
        return $this->comments()->whereNotNull('rating')->avg('rating') ?? 0;
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function isLikedBy($user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
