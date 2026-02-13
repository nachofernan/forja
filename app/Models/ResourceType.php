<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    protected $fillable = [
        'name',
        'tier',
        'description',
        'passive_generation_base',
        'icon',
        'sort_order'
    ];

    protected $casts = [
        'passive_generation_base' => 'decimal:2'
    ];

    // Relación: Un tipo de recurso tiene muchos recursos en inventario
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    // Relación: Un tipo puede ser output de muchas recetas
    public function recipesAsOutput()
    {
        return $this->hasMany(Recipe::class, 'output_resource_type_id');
    }

    // Relación: Un tipo puede ser input de muchas recetas
    public function recipeInputs()
    {
        return $this->hasMany(RecipeInput::class);
    }
}