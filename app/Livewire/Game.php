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
    public $currentDay, $currentTurnInDay, $turnsPerDay, $maxDays;
    public $gameOver = false, $dayComplete = false, $isVictory = false;
    public $countdown = 2; // El latido del juego
    public $sealProgress = 0;
    public $nextSealCost = [];
    
    public $resourcesByTier = [];
    public $upgradesByCategory = [];

    protected $listeners = ['nextTurn', 'nextDay', 'upgradeAllTier', 'toggleProductionTier', 'toggleAllProduction'];

    public function mount() { $this->loadGameData(); }

    public function nextTurn()
    {
        if ($this->gameOver || $this->dayComplete || $this->isVictory) return;

        // Lógica de "Un solo latido":
        // El poll de 1s descuenta el contador. Solo cuando llega a 1 procesamos el turno.
        if ($this->countdown <= 1) {
            $gameState = GameState::first();
            $gameState->processTurn();
            
            // Sincronizamos valores tras procesar
            $this->currentTurnInDay = $gameState->current_turn_in_day;
            if ($this->currentTurnInDay >= $this->turnsPerDay) {
                $this->dayComplete = true;
            }
            $this->countdown = 3; // Reset a 3 segundos para el próximo ciclo
        } else {
            $this->countdown--;
        }

        $this->loadGameData();
    }

    public function loadGameData()
    {
        $gameState = GameState::first();
        if (!$gameState) return;

        $this->currentDay = $gameState->current_day;
        $this->currentTurnInDay = $gameState->current_turn_in_day;
        $this->turnsPerDay = $gameState->turns_per_day;
        $this->maxDays = $gameState->max_days;
        $this->sealProgress = $gameState->primordial_seal_progress;
        $this->isVictory = $gameState->is_victory;
        $this->nextSealCost = $gameState->getNextSealCost();

        // Corregido: Usamos 'recipesAsOutput' que es el nombre real en ResourceType
        $allResources = Resource::with([
            'resourceType.recipesAsOutput.inputs.resourceType', 
            'resourceType.recipesAsOutput.automation'
        ])->get();

        $this->resourcesByTier = $allResources->groupBy(fn($r) => $r->resourceType->tier)
            ->map(fn($tier) => $tier->map(fn($res) => $this->formatResource($res, $allResources)))
            ->toArray();

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
                    'qty' => $c->quantity_required,
                    'has' => $allResources->firstWhere('resource_type_id', $c->resource_type_id)->quantity ?? 0
                ]),
                'req' => $u->requiresUpgrade ? ['name' => $u->requiresUpgrade->name, 'done' => $u->requiresUpgrade->is_purchased] : null
            ])->groupBy('category')->toArray();
    }

    private function formatResource($res, $allResources) {
        // Obtenemos la primera receta que produzca este recurso
        $recipe = $res->resourceType->recipesAsOutput->first();
        $upgradeCost = pow(2, $res->production_level);
        return [
            'id' => $res->id,
            'name' => $res->resourceType->name,
            'icon' => $res->resourceType->icon,
            'qty' => $res->quantity,
            'max' => pow(2, $res->production_level + 1),
            'level' => $res->production_level,
            'eff' => round($res->getEfficiency() * 100),
            'upgrade_cost' => $upgradeCost,
            'can_upgrade' => $res->quantity >= $upgradeCost,
            'recipe' => $recipe ? [
                'id' => $recipe->id,
                'active' => $recipe->automation->is_active ?? false,
                'inputs' => $recipe->inputs->map(fn($i) => ['icon' => $i->resourceType->icon, 'qty' => $i->quantity_required])
            ] : null
        ];
    }

    public function buyUpgrade($id) {
        $upgrade = Upgrade::find($id);
        if ($upgrade && $upgrade->purchase()) {
            $this->loadGameData();
        }
    }

    public function upgradeResource($id) {
        if(Resource::find($id)?->upgradeProductionLevel()) $this->loadGameData();
    }

    public function toggleRecipeAutomation($id) {
        $a = RecipeAutomation::where('recipe_id', $id)->first();
        if($a) { $a->is_active = !$a->is_active; $a->save(); $this->loadGameData(); }
    }
}