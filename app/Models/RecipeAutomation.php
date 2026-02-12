<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeAutomation extends Model
{
    protected $table = 'recipe_automation';

    protected $fillable = [
        'recipe_id',
        'is_active',
        'production_percentage'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'production_percentage' => 'integer'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    // Inicializa automatizaciones para todas las recetas
    public static function initialize()
    {
        $recipes = Recipe::all();
        
        foreach ($recipes as $recipe) {
            self::firstOrCreate(
                ['recipe_id' => $recipe->id],
                [
                    'is_active' => true,
                    'production_percentage' => 100
                ]
            );
        }
    }
}