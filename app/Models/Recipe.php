<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'output_resource_type_id',
        'output_quantity',
        'is_unlocked',
        'sort_order'
    ];

    protected $casts = [
        'is_unlocked' => 'boolean'
    ];

    
    // Trae la receta correspondiente a un ResourceTypeID
    public static function findByOutputResourceTypeId($resourceTypeId)
    {
        return self::where('output_resource_type_id', $resourceTypeId)->first();
    }
    

    // Recurso que produce
    public function outputResourceType()
    {
        return $this->belongsTo(ResourceType::class, 'output_resource_type_id');
    }

    // Ingredientes necesarios
    public function inputs()
    {
        return $this->hasMany(RecipeInput::class);
    }

    // Configuración de automatización
    public function automation()
    {
        return $this->hasOne(RecipeAutomation::class);
    }

    // Requisitos para desbloquear
    public function unlockRequirements()
    {
        return $this->hasMany(RecipeUnlockRequirement::class);
    }

    protected function getAdjustedInputCost($baseQuantity)
    {
        $outputResource = Resource::where('resource_type_id', $this->output_resource_type_id)->first();
        
        if (!$outputResource) {
            return $baseQuantity;
        }
        
        // Usar eficiencia total del recurso (nivel + upgrades)
        $totalEfficiency = $outputResource->getEfficiency();
        
        // Calcular costo ajustado
        $adjustedCost = $baseQuantity / $totalEfficiency;
        
        return ceil($adjustedCost);
    }

    /**
     * Obtiene la eficiencia total para mostrar en UI
     * Ahora simplemente delega al Resource
     * 
     * @return float Multiplicador de eficiencia total
     */
    public function getTotalEfficiency()
    {
        $outputResource = Resource::where('resource_type_id', $this->output_resource_type_id)->first();
        
        if (!$outputResource) {
            return 1.0;
        }
        
        return $outputResource->getEfficiency();
    }

    /**
     * Obtiene el porcentaje de eficiencia total para mostrar en UI
     * 
     * @return int Porcentaje de eficiencia (ej: 218 = 218%)
     */
    public function getTotalEfficiencyPercent()
    {
        return round($this->getTotalEfficiency() * 100);
    }

    // Helper: verifica si se pueden craftear X unidades
    public function canCraft($quantity = 1)
    {
        foreach ($this->inputs as $input) {
            $resource = Resource::where('resource_type_id', $input->resource_type_id)->first();
            $adjustedCost = $this->getAdjustedInputCost($input->quantity_required);
            
            if (!$resource || $resource->quantity < ($adjustedCost * $quantity)) {
                return false;
            }
        }
        return true;
    }

    // Helper: calcula cuántas unidades se pueden craftear con recursos actuales
    public function getMaxCraftable()
    {
        $maxCraftable = PHP_INT_MAX;
        
        foreach ($this->inputs as $input) {
            $resource = Resource::where('resource_type_id', $input->resource_type_id)->first();
            if (!$resource) {
                return 0;
            }
            
            $adjustedCost = $this->getAdjustedInputCost($input->quantity_required);
            $possibleFromThisResource = floor($resource->quantity / $adjustedCost);
            $maxCraftable = min($maxCraftable, $possibleFromThisResource);
        }
        
        return $maxCraftable == PHP_INT_MAX ? 0 : $maxCraftable;
    }

    // Helper: verifica si se puede desbloquear
    public function canUnlock()
    {
        if ($this->is_unlocked) {
            return false;
        }

        foreach ($this->unlockRequirements as $requirement) {
            $resource = Resource::where('resource_type_id', $requirement->resource_type_id)->first();
            if (!$resource || $resource->quantity < $requirement->quantity_required) {
                return false;
            }
        }
        
        return true;
    }

    // Helper: desbloquea la receta consumiendo recursos
    public function unlock()
    {
        if (!$this->canUnlock()) {
            return false;
        }

        foreach ($this->unlockRequirements as $requirement) {
            $resource = Resource::where('resource_type_id', $requirement->resource_type_id)->first();
            $resource->consume($requirement->quantity_required);
        }

        $this->is_unlocked = true;
        $this->save();

        return true;
    }

    // Helper: craftea X unidades (consume recursos y genera output)
    public function craft($quantity = 1)
    {
        if (!$this->canCraft($quantity)) {
            return 0;
        }

        $outputResource = Resource::where('resource_type_id', $this->output_resource_type_id)->first();
        if (!$outputResource) {
            return 0;
        }

        $maxStock = $outputResource->getMaxStock();
        $availableSpace = max(0, $maxStock - $outputResource->quantity);
        
        // Ajustar cantidad a lo que realmente cabe en el inventario
        $actualQuantity = min($quantity, floor($availableSpace / $this->output_quantity));
        
        if ($actualQuantity <= 0) {
            return 0; // No hay espacio, no se craftea nada, recursos no se consumen
        }

        // Consumir recursos solo por lo que realmente se va a craftear
        foreach ($this->inputs as $input) {
            $resource = Resource::where('resource_type_id', $input->resource_type_id)->first();
            $adjustedCost = $this->getAdjustedInputCost($input->quantity_required);
            $resource->consume($adjustedCost * $actualQuantity);
        }

        // Generar output (respeta tope de stock automáticamente)
        $outputResource->add($this->output_quantity * $actualQuantity);

        return $actualQuantity;
    }

    // Procesa la producción automática de esta receta
    public function processAutomatedProduction()
    {
        // No procesar si no está desbloqueada
        if (!$this->is_unlocked) {
            return 0;
        }

        $automation = $this->automation;
        
        if (!$automation || !$automation->is_active) {
            return 0;
        }

        $outputResource = Resource::where('resource_type_id', $this->output_resource_type_id)->first();
        if (!$outputResource) {
            return 0;
        }

        // CAMBIO PRINCIPAL: Aplicar eficiencia completa a la producción
        $baseProduction = $outputResource->production_level;
        $efficiencyMultiplier = $outputResource->getEfficiency();
        
        // Producción máxima considerando eficiencia
        $maxProduction = ceil($baseProduction * $efficiencyMultiplier);
        
        // Ajustar por porcentaje configurado
        $targetProduction = ceil($maxProduction * ($automation->production_percentage / 100));

        // Cantidad que realmente se puede craftear (considerando recursos y espacio)
        $actualProduction = min($targetProduction, $this->getMaxCraftable());

        if ($actualProduction > 0) {
            return $this->craft($actualProduction);
        }

        return 0;
    }
}