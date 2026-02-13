<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameState;
use App\Models\Resource;
use App\Models\Recipe;
use App\Models\RecipeAutomation;
use App\Models\Upgrade;

class Game extends Component
{
    // Estado básico
    public $currentDay, $currentTurnInDay, $turnsPerDay, $maxDays;
    public $gameOver = false, $dayComplete = false, $isVictory = false;
    public $countdown = 3; // Subido a 3s para estabilidad
    public $sealProgress = 0;
    public $nextSealCost = [];
    
    // Datos procesados para la vista
    public $resourcesByTier = [];
    public $upgradesByCategory = [];

    protected $listeners = ['nextTurn', 'nextDay', 'upgradeAllTier', 'toggleProductionTier', 'toggleAllProduction'];

    public function mount() { $this->initializeGame(); }

    public function initializeGame()
    {
        GameState::truncate();
        Resource::truncate();
        RecipeAutomation::truncate();
        Upgrade::initialize();
        $gameState = GameState::initialize();
        Resource::initialize();
        
        $this->syncState($gameState);
        $this->loadGameData();
    }

    private function syncState($gameState)
    {
        $this->currentDay = $gameState->current_day;
        $this->currentTurnInDay = $gameState->current_turn_in_day;
        $this->turnsPerDay = $gameState->turns_per_day;
        $this->maxDays = $gameState->max_days;
        $this->sealProgress = $gameState->primordial_seal_progress;
        $this->isVictory = $gameState->is_victory;
        $this->nextSealCost = $gameState->getNextSealCost();
    }

    public function nextTurn()
    {
        if ($this->gameOver || $this->dayComplete || $this->isVictory) return;

        if ($this->countdown > 0) {
            $this->countdown--;
            return;
        }

        $gameState = GameState::first();
        $gameState->processTurn();
        $this->currentTurnInDay++;
        
        if ($this->currentTurnInDay >= $this->turnsPerDay) {
            $this->dayComplete = true;
        }
        
        $gameState->update(['current_turn_in_day' => $this->currentTurnInDay]);
        $this->countdown = 3;
        $this->loadGameData();
    }

    public function loadGameData()
    {
        $gameState = GameState::first();
        $this->syncState($gameState);

        // Pre-procesar Recursos y Recetas para evitar lógica en Blade
        $allResources = Resource::with('resourceType')->get();
        $allRecipes = Recipe::with(['outputResourceType', 'inputs.resourceType', 'automation', 'unlockRequirements.resourceType'])->get();

        $this->resourcesByTier = $allResources->groupBy(fn($r) => $r->resourceType->tier)
            ->map(function($tierGroup) use ($allRecipes, $allResources) {
                return $tierGroup->map(function($res) use ($allRecipes, $allResources) {
                    $recipe = $allRecipes->firstWhere('output_resource_type_id', $res->resource_type_id);
                    $upgradeCost = pow(2, $res->production_level);
                    $maxStock = pow(2, $res->production_level + 1);
                    
                    return [
                        'id' => $res->id,
                        'name' => $res->resourceType->name,
                        'icon' => $res->resourceType->icon,
                        'tier' => $res->resourceType->tier,
                        'quantity' => $res->quantity,
                        'max_stock' => $maxStock,
                        'stock_percent' => min(100, round(($res->quantity / $maxStock) * 100)),
                        'level' => $res->production_level,
                        'upgrade_cost' => $upgradeCost,
                        'can_upgrade' => $res->quantity >= $upgradeCost,
                        'efficiency_percent' => $res->getEfficiency() * 100,
                        'recipe' => $recipe ? $this->formatRecipeData($recipe, $res, $allResources) : null
                    ];
                });
            })->toArray();

        // Upgrades agrupados
        $this->upgradesByCategory = Upgrade::with(['costs.resourceType', 'requiresUpgrade'])
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'description' => $u->description,
                'category' => $u->category,
                'is_purchased' => $u->is_purchased,
                'can_purchase' => $u->canPurchase(),
                'costs' => $u->costs->map(fn($c) => [
                    'icon' => $c->resourceType->icon,
                    'name' => $c->resourceType->name,
                    'required' => $c->quantity_required,
                    'current' => $allResources->firstWhere('resource_type_id', $c->resource_type_id)->quantity ?? 0
                ]),
                'req' => $u->requiresUpgrade ? ['name' => $u->requiresUpgrade->name, 'done' => $u->requiresUpgrade->is_purchased] : null
            ])->groupBy('category')->toArray();
    }

    private function formatRecipeData($recipe, $resource, $allResources)
    {
        $efficiency = $recipe->getTotalEfficiency();
        return [
            'id' => $recipe->id,
            'is_unlocked' => $recipe->is_unlocked,
            'can_unlock' => $recipe->canUnlock(),
            'is_active' => $recipe->automation->is_active ?? false,
            'percentage' => $recipe->automation->production_percentage ?? 100,
            'inputs' => $recipe->inputs->map(fn($i) => [
                'icon' => $i->resourceType->icon,
                'cost' => ceil($i->quantity_required / $efficiency)
            ]),
            'reqs' => $recipe->unlockRequirements->map(fn($req) => [
                'icon' => $req->resourceType->icon,
                'name' => $req->resourceType->name,
                'required' => $req->quantity_required,
                'current' => $allResources->firstWhere('resource_type_id', $req->resource_type_id)->quantity ?? 0
            ])
        ];
    }

    // Handlers (Simplificados)
    public function upgradeResource($id) { if(Resource::find($id)?->upgradeProductionLevel()) $this->loadGameData(); }
    public function toggleRecipeAutomation($id) { 
        $a = RecipeAutomation::where('recipe_id', $id)->first();
        if($a){ $a->is_active = !$a->is_active; $a->save(); $this->loadGameData(); }
    }
    public function depositEternalAlloy() {
        $res = GameState::first()->depositEternalAlloy();
        session()->flash('hotkey_message', $res['message']);
        $this->loadGameData();
    }
}