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
        'ingredients',
        'instructions',
        'image',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'category',
        'is_vegetarian',
        'is_published',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'is_vegetarian' => 'boolean',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->title);
        });
    }

    // Accessor for total time
    public function getTotalTimeAttribute()
    {
        return $this->prep_time + $this->cook_time;
    }
}
