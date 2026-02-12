<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeInput extends Model
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
}