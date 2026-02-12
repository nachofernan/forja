<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'effect_type',
        'target_resource_type_id',
        'target_tier',
        'effect_value',
        'is_purchased',
        'requires_upgrade_id',
        'sort_order'
    ];

    protected $casts = [
        'is_purchased' => 'boolean',
        'effect_value' => 'decimal:2'
    ];

    // Recurso objetivo (para upgrades específicos)
    public function targetResourceType()
    {
        return $this->belongsTo(ResourceType::class, 'target_resource_type_id');
    }

    // Inicializa el estado de upgrades (resetea todos a no comprados)
    public static function initialize()
    {
        self::query()->update(['is_purchased' => false]);
    }

    // Upgrade requerido (para cadenas de mejoras)
    public function requiresUpgrade()
    {
        return $this->belongsTo(Upgrade::class, 'requires_upgrade_id');
    }

    // Costos del upgrade
    public function costs()
    {
        return $this->hasMany(UpgradeCost::class);
    }

    // Verifica si se puede comprar
    public function canPurchase()
    {
        if ($this->is_purchased) {
            return false;
        }

        // Verificar prerequisito
        if ($this->requires_upgrade_id) {
            $required = Upgrade::find($this->requires_upgrade_id);
            if (!$required || !$required->is_purchased) {
                return false;
            }
        }

        // Verificar recursos
        foreach ($this->costs as $cost) {
            $resource = Resource::where('resource_type_id', $cost->resource_type_id)->first();
            if (!$resource || $resource->quantity < $cost->quantity_required) {
                return false;
            }
        }

        return true;
    }

    // Compra el upgrade
    public function purchase()
    {
        if (!$this->canPurchase()) {
            return false;
        }

        // Consumir recursos
        foreach ($this->costs as $cost) {
            $resource = Resource::where('resource_type_id', $cost->resource_type_id)->first();
            $resource->consume($cost->quantity_required);
        }

        // Marcar como comprado
        $this->is_purchased = true;
        $this->save();

        return true;
    }

    // ========== MÉTODOS ESTÁTICOS PARA APLICAR EFECTOS ==========

    // Obtiene el multiplicador total de generación para un recurso específico
    public static function getGenerationMultiplier($resourceTypeId)
    {
        $multiplier = 1.0;

        // Upgrades específicos del recurso
        $specificUpgrades = self::where('is_purchased', true)
            ->where('effect_type', 'multiply_generation')
            ->where('target_resource_type_id', $resourceTypeId)
            ->get();

        foreach ($specificUpgrades as $upgrade) {
            $multiplier *= $upgrade->effect_value;
        }

        // Upgrades de tier completo (ej: Armonía Elemental)
        $resource = ResourceType::find($resourceTypeId);
        if ($resource) {
            $tierUpgrades = self::where('is_purchased', true)
                ->where('effect_type', 'multiply_generation')
                ->where('target_tier', $resource->tier)
                ->get();

            foreach ($tierUpgrades as $upgrade) {
                $multiplier *= $upgrade->effect_value;
            }
        }

        return $multiplier;
    }

    // Obtiene el bonus total de turnos
    public static function getTotalTurnBonus()
    {
        return self::where('is_purchased', true)
            ->where('effect_type', 'add_turns')
            ->sum('effect_value');
    }

    // Obtiene el bonus total de días
    public static function getTotalDayBonus()
    {
        return self::where('is_purchased', true)
            ->where('effect_type', 'add_days')
            ->sum('effect_value');
    }

    // Obtiene la reducción total de costo para una receta específica
    public static function getRecipeCostReduction($outputResourceTypeId)
    {
        $reduction = 0;

        // Upgrades específicos del recurso output
        $specificUpgrades = self::where('is_purchased', true)
            ->where('effect_type', 'reduce_recipe_cost')
            ->where('target_resource_type_id', $outputResourceTypeId)
            ->get();

        foreach ($specificUpgrades as $upgrade) {
            $reduction += $upgrade->effect_value;
        }

        // Upgrades de tier completo
        $resource = ResourceType::find($outputResourceTypeId);
        if ($resource) {
            $tierUpgrades = self::where('is_purchased', true)
                ->where('effect_type', 'reduce_recipe_cost')
                ->where('target_tier', $resource->tier)
                ->get();

            foreach ($tierUpgrades as $upgrade) {
                $reduction += $upgrade->effect_value;
            }
        }

        return min($reduction, 0.95); // Máximo 95% de reducción
    }
}