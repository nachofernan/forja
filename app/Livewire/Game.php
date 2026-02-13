<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameState;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Recipe;
use App\Models\RecipeAutomation;
use App\Models\RecipeUnlockRequirement;
use App\Models\Upgrade;

class Game extends Component
{
    public $currentDay = 1;
    public $currentTurnInDay = 0;
    public $turnsPerDay = 10;
    public $maxDays = 50;
    public $gameOver = false;
    public $countdown = 2;
    public $dayComplete = false;

    public $sealProgress = 0;
    public $isVictory = false;
    public $nextSealCost = [];

    // Recursos agrupados por tier
    public $resourcesByTier = [];
    
    // Recetas disponibles
    public $recipes = [];

    // Upgrades disponibles
    public $upgrades = [];

    protected $listeners = [
        'nextTurn', 
        'nextDay',
        'upgradeAllTier', // NUEVO
        'toggleProductionTier', // NUEVO
        'toggleAllProduction' // NUEVO
    ];

    public function mount()
    {
        $this->initializeGame();
    }

    public function initializeGame()
    {
        // Reset estado
        $this->currentDay = 1;
        $this->currentTurnInDay = 0;
        $this->turnsPerDay = 10;
        $this->maxDays = 50;
        $this->countdown = 1;
        $this->gameOver = false;
        $this->dayComplete = false;
        
        // Reset database
        GameState::truncate();
        Resource::truncate();
        RecipeAutomation::truncate();
        
        // Initialize
        Upgrade::initialize(); // Reset upgrades
        $gameState = GameState::initialize();
        Resource::initialize();
        RecipeAutomation::initialize();
        RecipeUnlockRequirement::initialize();
        
        $this->currentDay = $gameState->current_day;
        $this->currentTurnInDay = $gameState->current_turn_in_day;
        $this->turnsPerDay = $gameState->turns_per_day;
        $this->maxDays = $gameState->max_days;

        $this->sealProgress = $gameState->primordial_seal_progress;
        $this->isVictory = $gameState->is_victory;
        $this->nextSealCost = $gameState->getNextSealCost();
        
        $this->loadGameData();
    }

    public function nextDay()
    {
        if (!$this->dayComplete) {
            return;
        }
        
        $gameState = GameState::first();
        
        if (($this->currentDay + 1) > $this->maxDays) {
            $this->gameOver = true;
        } else {
            $this->currentDay++;
            $this->currentTurnInDay = 0;
            $this->dayComplete = false;
            $this->countdown = 1;
            
            $gameState->update([
                'current_day' => $this->currentDay,
                'current_turn_in_day' => 0
            ]);
        }
    }

    public function nextTurn()
    {
        if ($this->gameOver || $this->dayComplete || $this->currentDay > $this->maxDays || $this->isVictory) {
            return;
        }

        // Countdown
        if ($this->countdown > 0) {
            $this->countdown--;
            return;
        }

        // Procesar turno completo
        $gameState = GameState::first();
        $gameState->processTurn();

        // Incrementar turno
        $this->currentTurnInDay++;
        
        // Verificar si el día se completó
        if ($this->currentTurnInDay >= $this->turnsPerDay) {
            $this->dayComplete = true;
            
            $gameState->update([
                'current_day' => $this->currentDay,
                'current_turn_in_day' => $this->currentTurnInDay
            ]);
            
            $this->loadGameData();
            return;
        }
        
        // Update normal
        $gameState->update([
            'current_day' => $this->currentDay,
            'current_turn_in_day' => $this->currentTurnInDay
        ]);
        
        $this->countdown = 1;
        $this->loadGameData();
    }

    public function depositEternalAlloy()
    {
        $gameState = GameState::first();
        $result = $gameState->depositEternalAlloy();
        
        if ($result['success']) {
            $this->sealProgress = $gameState->primordial_seal_progress;
            $this->isVictory = $gameState->is_victory;
            $this->nextSealCost = $gameState->getNextSealCost();
            $this->loadGameData();
        }
        
        // Podrías agregar un mensaje flash aquí si querés feedback
        session()->flash('message', $result['message']);
    }

    public function upgradeResource($resourceId)
    {
        $resource = Resource::find($resourceId);
        
        if ($resource && $resource->upgradeProductionLevel()) {
            $this->loadGameData();
        }
    }

    public function toggleRecipeAutomation($recipeId)
    {
        $automation = RecipeAutomation::where('recipe_id', $recipeId)->first();
        
        if ($automation) {
            $automation->is_active = !$automation->is_active;
            $automation->save();
            $this->loadGameData();
        }
    }

    public function updateRecipePercentage($recipeId, $percentage)
    {
        $automation = RecipeAutomation::where('recipe_id', $recipeId)->first();
        
        if ($automation) {
            $automation->production_percentage = $percentage;
            $automation->save();
        }
    }

    public function unlockRecipe($recipeId)
    {
        $recipe = Recipe::with('unlockRequirements')->find($recipeId);
        
        if ($recipe && $recipe->unlock()) {
            $this->loadGameData();
        }
    }

    public function purchaseUpgrade($upgradeId)
    {
        $upgrade = Upgrade::with('costs')->find($upgradeId);
        
        if ($upgrade && $upgrade->purchase()) {
            // Reinicializar GameState para aplicar cambios de turnos/días
            GameState::initialize();
            
            $gameState = GameState::first();
            $this->turnsPerDay = $gameState->turns_per_day;
            $this->maxDays = $gameState->max_days;
            
            $this->loadGameData();
        }
    }

    protected function loadGameData()
    {
        // Cargar recursos agrupados por tier
        $this->resourcesByTier = Resource::with('resourceType')
            ->get()
            ->groupBy(function($resource) {
                return $resource->resourceType->tier;
            })
            ->sortKeys()
            ->toArray();

        // Cargar recetas con su automatización
        $this->recipes = Recipe::with(['outputResourceType', 'inputs.resourceType', 'automation', 'unlockRequirements.resourceType'])
            ->orderBy('sort_order')
            ->get()
            ->map(function($recipe) {
                $outputResource = Resource::where('resource_type_id', $recipe->output_resource_type_id)->first();
                
                // Calcular eficiencia total (nivel + upgrades)
                $totalEfficiency = $recipe->getTotalEfficiency();
                $totalEfficiencyPercent = $recipe->getTotalEfficiencyPercent();
                
                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'is_unlocked' => $recipe->is_unlocked,
                    'output' => [
                        'name' => $recipe->outputResourceType->name,
                        'icon' => $recipe->outputResourceType->icon,
                        'quantity' => $recipe->output_quantity,
                    ],
                    'inputs' => $recipe->inputs->map(function($input) {
                        return [
                            'name' => $input->resourceType->name,
                            'icon' => $input->resourceType->icon,
                            'required' => $input->quantity_required,
                        ];
                    })->toArray(),
                    'unlock_requirements' => $recipe->unlockRequirements->map(function($req) {
                        $resource = Resource::where('resource_type_id', $req->resource_type_id)->first();
                        return [
                            'name' => $req->resourceType->name,
                            'icon' => $req->resourceType->icon,
                            'required' => $req->quantity_required,
                            'current' => $resource ? $resource->quantity : 0,
                        ];
                    })->toArray(),
                    'can_unlock' => $recipe->canUnlock(),
                    'production_level' => $outputResource ? $outputResource->production_level : 1,
                    'efficiency' => $totalEfficiency, // Multiplicador (ej: 2.18 = 218%)
                    'efficiency_percent' => $totalEfficiencyPercent, // Porcentaje para UI (ej: 218)
                    'automation' => [
                        'is_active' => $recipe->automation->is_active ?? true,
                        'percentage' => $recipe->automation->production_percentage ?? 100,
                    ]
                ];
            })
            ->toArray();

        // Cargar upgrades
        $this->upgrades = Upgrade::with(['costs.resourceType', 'targetResourceType', 'requiresUpgrade'])
            ->orderBy('sort_order')
            ->get()
            ->map(function($upgrade) {
                return [
                    'id' => $upgrade->id,
                    'code' => $upgrade->code,
                    'name' => $upgrade->name,
                    'description' => $upgrade->description,
                    'category' => $upgrade->category,
                    'is_purchased' => $upgrade->is_purchased,
                    'can_purchase' => $upgrade->canPurchase(),
                    'costs' => $upgrade->costs->map(function($cost) {
                        $resource = Resource::where('resource_type_id', $cost->resource_type_id)->first();
                        return [
                            'name' => $cost->resourceType->name,
                            'icon' => $cost->resourceType->icon,
                            'required' => $cost->quantity_required,
                            'current' => $resource ? $resource->quantity : 0,
                        ];
                    })->toArray(),
                    'requires_upgrade' => $upgrade->requiresUpgrade ? [
                        'name' => $upgrade->requiresUpgrade->name,
                        'is_purchased' => $upgrade->requiresUpgrade->is_purchased
                    ] : null,
                ];
            })
            ->toArray();

        $gameState = GameState::first();
        $this->sealProgress = $gameState->primordial_seal_progress;
        $this->isVictory = $gameState->is_victory;
        $this->nextSealCost = $gameState->getNextSealCost();
    }

    /**
     * Intenta upgradear todos los recursos de un tier específico
     */
    public function upgradeAllTier($tier)
    {
        $resources = Resource::whereHas('resourceType', function($q) use ($tier) {
            $q->where('tier', $tier);
        })->get();
        
        $upgraded = 0;
        foreach ($resources as $resource) {
            if ($resource->upgradeProductionLevel()) {
                $upgraded++;
            }
        }
        
        if ($upgraded > 0) {
            $this->loadGameData();
            session()->flash('hotkey_message', "✓ Tier $tier mejorado ($upgraded recursos actualizados)");
        } else {
            session()->flash('hotkey_message', "✗ No hay suficientes recursos para mejorar Tier $tier");
        }
    }

    /**
     * Toggle de producción para todas las recetas de un tier
     */
    public function toggleProductionTier($tier)
    {
        $recipes = Recipe::whereHas('outputResourceType', function($q) use ($tier) {
            $q->where('tier', $tier);
        })->where('is_unlocked', true)->get();
        
        if ($recipes->isEmpty()) {
            session()->flash('hotkey_message', "✗ No hay recetas desbloqueadas en Tier $tier");
            return;
        }
        
        // Determinar el estado mayoritario (si la mayoría está activa, apagamos todo, sino encendemos)
        $activeCount = 0;
        foreach ($recipes as $recipe) {
            if ($recipe->automation && $recipe->automation->is_active) {
                $activeCount++;
            }
        }
        
        $newState = $activeCount < ($recipes->count() / 2);
        
        foreach ($recipes as $recipe) {
            $automation = RecipeAutomation::where('recipe_id', $recipe->id)->first();
            if ($automation) {
                $automation->is_active = $newState;
                $automation->save();
            }
        }
        
        $this->loadGameData();
        $stateText = $newState ? 'activada' : 'pausada';
        session()->flash('hotkey_message', "✓ Producción Tier $tier $stateText ({$recipes->count()} recetas)");
    }

    /**
     * Toggle de producción para TODAS las recetas (Tiers 1-3)
     */
    public function toggleAllProduction()
    {
        $recipes = Recipe::whereHas('outputResourceType', function($q) {
            $q->where('tier', '>', 0);
        })->where('is_unlocked', true)->get();
        
        if ($recipes->isEmpty()) {
            return;
        }
        
        // Determinar estado mayoritario
        $activeCount = 0;
        foreach ($recipes as $recipe) {
            if ($recipe->automation && $recipe->automation->is_active) {
                $activeCount++;
            }
        }
        
        $newState = $activeCount < ($recipes->count() / 2);
        
        foreach ($recipes as $recipe) {
            $automation = RecipeAutomation::where('recipe_id', $recipe->id)->first();
            if ($automation) {
                $automation->is_active = $newState;
                $automation->save();
            }
        }
        
        $this->loadGameData();
        $stateText = $newState ? 'activada' : 'pausada';
        session()->flash('hotkey_message', "✓ TODA la producción $stateText ({$recipes->count()} recetas)");
    }

    public function render()
    {
        return view('livewire.game', [
            'currentDay' => $this->currentDay,
            'maxDays' => $this->maxDays,
            'currentTurnInDay' => $this->currentTurnInDay,
            'turnsPerDay' => $this->turnsPerDay,
            'resourcesByTier' => $this->resourcesByTier,
            'recipes' => $this->recipes,
            'upgrades' => $this->upgrades,
            'gameOver' => $this->gameOver,
            'dayComplete' => $this->dayComplete,
            'countdown' => $this->countdown
        ]);
    }
}