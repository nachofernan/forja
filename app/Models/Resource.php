<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'resource_type_id',
        'quantity',
        'production_level',
        'total_generated',
        'total_consumed'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'production_level' => 'integer',
        'total_generated' => 'integer',
        'total_consumed' => 'integer'
    ];

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }

    // Inicializa todos los recursos del juego con cantidad 0
    public static function initialize()
    {
        $resourceTypes = ResourceType::all();
        
        foreach ($resourceTypes as $type) {
            self::firstOrCreate(
                ['resource_type_id' => $type->id],
                [
                    'quantity' => 0,
                    'production_level' => 1,
                    'total_generated' => 0,
                    'total_consumed' => 0
                ]
            );
        }

        // Inicializar recursos Tier 0 con 10000
        if(false){
            foreach ($resourceTypes as $type) {
                if ($type->tier === 0) {
                   $resource = self::where('resource_type_id', $type->id)->first();
                   $resource->quantity = 10000; 
                   $resource->save();
                }
            }
        }

    }

    // Calcula el stock máximo según el nivel
    public function getMaxStock()
    {
        $maxStock = pow(2, $this->production_level + 1);
        return $maxStock;
    }

    /**
     * Calcula la eficiencia total de producción (nivel + upgrades)
     * 
     * - Nivel 1 = 100% (1.0)
     * - Nivel 2 = 110% (1.1)
     * - Nivel 10 = 190% (1.9)
     * 
     * Luego se multiplica por (1 + reducción_upgrades)
     * Ej: Nivel 10 (1.9) + 15% upgrade = 1.9 * 1.15 = 2.185
     * 
     * @return float Multiplicador de eficiencia total
     */
    public function getEfficiency()
    {
        // Eficiencia base por nivel del recurso
        $levelEfficiency = 1 + (($this->production_level - 1) * 0.1);
        
        // Reducción adicional por upgrades
        $upgradeReduction = Upgrade::getRecipeCostReduction($this->resource_type_id);
        
        // Combinar ambos efectos (multiplicativo)
        return $levelEfficiency * (1 + $upgradeReduction);
    }

    /**
     * Obtiene el porcentaje de eficiencia para mostrar en UI
     * 
     * @return int Porcentaje (100, 110, 120, ..., 190, 200, etc)
     */
    public function getEfficiencyPercent()
    {
        return 100 + (($this->production_level - 1) * 10);
    }

    // Helper para agregar cantidad (respeta tope de stock)
    public function add($amount)
    {
        $maxStock = $this->getMaxStock();
        $availableSpace = max(0, $maxStock - $this->quantity);
        $actualAdded = min($amount, $availableSpace);
        
        $this->quantity += $actualAdded;
        $this->total_generated += $actualAdded;
        $this->save();
        
        return $actualAdded; // Retorna cuánto realmente se agregó
    }

    // Helper para consumir cantidad
    public function consume($amount)
    {
        if ($this->quantity < $amount) {
            return false;
        }
        
        $this->quantity -= $amount;
        $this->total_consumed += $amount;
        $this->save();
        return true;
    }

    // Costo para subir al siguiente nivel (potencia de 2)
    public function getUpgradeCost()
    {
        return pow(2, $this->production_level);
    }

    // Intenta mejorar el nivel de producción
    public function upgradeProductionLevel()
    {
        $cost = $this->getUpgradeCost();
        
        if ($this->quantity >= $cost) {
            $this->quantity -= $cost;
            $this->total_consumed += $cost;
            $this->production_level++;
            $this->save();
            return true;
        }
        
        return false;
    }

    // Generación pasiva por turno (solo Tier 0) - INTEGRADO CON UPGRADES
    public function generatePassive()
    {
        if ($this->resourceType->tier === 0) {
            // Generación base
            $baseAmount = $this->resourceType->passive_generation_base * $this->production_level;
            
            // Aplicar multiplicador de upgrades
            $multiplier = Upgrade::getGenerationMultiplier($this->resource_type_id);
            $amount = $baseAmount * $multiplier;
            
            $actualAdded = $this->add($amount);
            
            // Si no se agregó todo, se perdió el excedente (por tope de stock)
            return $actualAdded;
        }
        
        return 0;
    }
}