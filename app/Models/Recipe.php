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
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
        'views' => 'integer',
        'rating' => 'decimal:1',
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
}
