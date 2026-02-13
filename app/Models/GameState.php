<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameState extends Model
{
    protected $fillable = [
        'current_turn_in_day',
        'max_turns_per_day',
        'current_day',
        'max_days',
        'turns_per_day',
        'started_at',
        'round_started_at',
        'total_turns_played', // NUEVO
        'primordial_seal_progress', // NUEVO
        'is_victory', // NUEVO
    ];
    
    protected $attributes = [
        'current_day' => 1,
        'max_days' => 50, //50
        'turns_per_day' => 10, //10
        'current_turn_in_day' => 0,
        'max_turns_per_day' => 10, //10
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'round_started_at' => 'datetime',
        'is_victory' => 'boolean',
        'primordial_seal_progress' => 'integer',
        'current_day' => 'integer',
        'current_turn_in_day' => 'integer'
    ];

    public static function initialize()
    {
        $gameState = self::first();
        
        // Calcular turnos y días con upgrades
        $baseTurns = 10; //10
        $baseDays = 50; //50
        
        $turnsBonus = Upgrade::getTotalTurnBonus();
        $daysBonus = Upgrade::getTotalDayBonus();
        
        $totalTurns = min($baseTurns + $turnsBonus, 100); // Máximo 100 turnos
        $totalDays = $baseDays + $daysBonus;
        
        if (!$gameState) {
            $gameState = self::create([
                'current_day' => 1,
                'current_turn_in_day' => 0,
                'turns_per_day' => $totalTurns,
                'max_days' => $totalDays,
                'max_turns_per_day' => $totalTurns,
                'started_at' => now()
            ]);
        } else {
            // Actualizar con los nuevos valores de upgrades
            $gameState->update([
                'turns_per_day' => $totalTurns,
                'max_turns_per_day' => $totalTurns,
                'max_days' => $totalDays
            ]);
        }
        
        return $gameState;
    }

    // Procesa un turno completo: genera recursos pasivos y procesa recetas
    public function processTurn()
    {
        // AGREGAR AL INICIO DEL MÉTODO:
        $this->total_turns_played++;
        $this->save();
        
        // 1. Generar recursos pasivos (Tier 0)
        $this->generatePassiveResources();

        // 2. Procesar recetas en orden de tiers (Tier 1, 2, 3)
        $this->processAutomatedRecipes();
    }

    // Genera recursos Tier 0 pasivamente
    protected function generatePassiveResources()
    {
        $tier0Resources = Resource::whereHas('resourceType', function($query) {
            $query->where('tier', 0);
        })->get();

        foreach ($tier0Resources as $resource) {
            $resource->generatePassive();
        }
    }

    // Procesa todas las recetas automatizadas en orden de tier
    protected function processAutomatedRecipes()
    {
        // Obtener recetas ordenadas por tier del output (1, 2, 3)
        $recipes = Recipe::with(['outputResourceType', 'automation'])
            ->whereHas('outputResourceType', function($query) {
                $query->where('tier', '>', 0);
            })
            ->get()
            ->sortBy(function($recipe) {
                return $recipe->outputResourceType->tier;
            });

        foreach ($recipes as $recipe) {
            $recipe->processAutomatedProduction();
        }
    }

    // Obtiene los turnos totales disponibles (considerando upgrades)
    public function getTotalTurnsPerDay()
    {
        $baseTurns = 15; //10
        $turnsBonus = Upgrade::getTotalTurnBonus();
        return min($baseTurns + $turnsBonus, 100);
    }

    // Obtiene los días totales disponibles (considerando upgrades)
    public function getTotalMaxDays()
    {
        $baseDays = 10; //50
        $daysBonus = Upgrade::getTotalDayBonus();
        return $baseDays + $daysBonus;
    }

    /**
     * Calcula el costo de la siguiente unidad del Sello Primordial
     */
    public function getNextSealCost()
    {
        $nextUnit = $this->primordial_seal_progress + 1;
        // Escalado de dificultad: aumenta significativamente después de la mitad
        $multiplier = $nextUnit > 5 ? 25 : 10;
        
        return [
            'adamantita' => $nextUnit * $multiplier,
            'oricalco' => $nextUnit * $multiplier,
            'unit_number' => $nextUnit
        ];
    }

    /**
     * Intenta depositar una unidad de Aleación Eterna en el Sello Primordial
     */
    public function depositEternalAlloy()
    {
        if ($this->is_victory) {
            return ['success' => false, 'message' => '¡El juego ya ha sido completado!'];
        }

        if ($this->primordial_seal_progress >= 10) {
            return ['success' => false, 'message' => 'El Sello Primordial ya está completo'];
        }

        $cost = $this->getNextSealCost();
        
        // Obtener recursos necesarios
        $adamantita = \App\Models\Resource::whereHas('resourceType', function($q) {
            $q->where('name', 'Adamantita');
        })->first();
        
        $oricalco = \App\Models\Resource::whereHas('resourceType', function($q) {
            $q->where('name', 'Oricalco');
        })->first();

        if (!$adamantita || !$oricalco) {
            return ['success' => false, 'message' => 'Recursos no encontrados'];
        }

        // Verificar si tiene suficientes recursos
        if ($adamantita->quantity < $cost['adamantita'] || $oricalco->quantity < $cost['oricalco']) {
            return [
                'success' => false, 
                'message' => "Necesitas {$cost['adamantita']} Adamantita y {$cost['oricalco']} Oricalco"
            ];
        }

        // Consumir recursos
        $adamantita->consume($cost['adamantita']);
        $oricalco->consume($cost['oricalco']);

        // Incrementar progreso
        $this->primordial_seal_progress++;
        
        // Verificar victoria
        if ($this->primordial_seal_progress >= 10) {
            $this->is_victory = true;
        }
        
        $this->save();

        return [
            'success' => true, 
            'message' => "Unidad {$cost['unit_number']}/10 depositada en el Sello Primordial",
            'victory' => $this->is_victory
        ];
    }
}