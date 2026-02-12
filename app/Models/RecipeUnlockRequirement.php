<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeUnlockRequirement extends Model
{
    protected $fillable = [
        'recipe_id',
        'resource_type_id',
        'quantity_required'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }

    // Inicializa bloqueo para todas las recetas
    public static function initialize()
    {
        $recipes = Recipe::all();
        
        foreach ($recipes as $recipe) {
            $recipe->is_unlocked = false;
            $recipe->save();
        }
    }
}