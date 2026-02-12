<div wire:poll.1s="nextTurn" class="min-h-screen w-full p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Game Header -->
        <div class="bg-slate-800/50 backdrop-blur rounded-lg shadow-xl p-6 mb-6 border border-slate-700 fixed top-0 left-0 right-0 z-50">
            @if($isVictory)
                <!-- PANTALLA DE VICTORIA -->
                <div class="text-center">
                    <div class="text-5xl font-bold text-yellow-400 mb-2 animate-pulse">
                        ⭐ ¡VICTORIA! ⭐
                    </div>
                    <div class="text-xl text-green-400 mb-4">
                        El Sello Primordial está completo. El eclipse ha sido detenido.
                    </div>
                    <div class="text-sm text-slate-400 mb-4">
                        Completado en el día {{ $currentDay }} • {{ $turnsPerDay }} turnos por día
                    </div>
                    <button 
                        wire:click="initializeGame"
                        class="px-8 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold text-lg"
                    >
                        🔄 Jugar de Nuevo
                    </button>
                </div>
            @else
                <!-- HEADER NORMAL -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center">
                        <div class="text-sm font-semibold text-slate-400 uppercase tracking-wide">Día</div>
                        <div class="text-4xl font-bold text-amber-400">
                            {{ $currentDay }} / {{ $maxDays }}
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm font-semibold text-slate-400 uppercase tracking-wide">Turno</div>
                        <div class="text-3xl font-bold text-blue-400">
                            {{ $currentTurnInDay }} / {{ $turnsPerDay }}
                        </div>
                    </div>
                    
                    <div class="text-center">
                        @if($dayComplete && !$gameOver)
                        <button 
                            wire:click="nextDay"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold"
                        >
                            Siguiente Día
                        </button>
                        @elseif($gameOver)
                            <button 
                                wire:click="initializeGame"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
                            >
                                Reiniciar Juego
                            </button>
                        @else
                            <div class="text-sm font-semibold text-slate-400 uppercase tracking-wide">Siguiente turno</div>
                            <div class="text-3xl font-bold text-pink-400">
                                {{ $countdown }}s
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="h-30"></div>

        <!-- Altar del Sello Primordial -->
        <div class="mb-8 bg-gradient-to-br from-purple-900/30 to-indigo-900/30 backdrop-blur rounded-lg shadow-2xl p-6 border-2 border-purple-500/50">
            <div class="text-center mb-4">
                <h2 class="text-3xl font-bold text-purple-300 mb-2">⭐ Sello Primordial ⭐</h2>
                <p class="text-sm text-slate-400">
                    @if($isVictory)
                        ¡El sello está completo! El eclipse ha sido detenido.
                    @else
                        Deposita 10 unidades de Aleación Eterna para completar el sello y detener el eclipse
                    @endif
                </p>
            </div>

            <!-- Barra de progreso -->
            <div class="grid grid-cols-10 gap-2 mb-6">
                @for($i = 1; $i <= 10; $i++)
                    <div class="aspect-square rounded-lg border-2 flex items-center justify-center {{ $i <= $sealProgress ? 'bg-yellow-500 border-yellow-400 animate-pulse' : 'bg-slate-800 border-slate-600' }}">
                        @if($i <= $sealProgress)
                            <span class="text-2xl">⭐</span>
                        @else
                            <span class="text-slate-600 text-xl">{{ $i }}</span>
                        @endif
                    </div>
                @endfor
            </div>

            <!-- Info y botón -->
            @if(!$isVictory)
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <div class="text-lg font-bold text-white mb-1">
                            Progreso: {{ $sealProgress }}/10 unidades
                        </div>
                        <div class="text-sm text-slate-400">
                            Siguiente unidad (#{{ $nextSealCost['unit_number'] ?? '?' }}) requiere:
                        </div>
                        <div class="flex gap-4 mt-2 justify-center sm:justify-start">
                            @php
                                $adamantita = collect($resourcesByTier[3] ?? [])->firstWhere('resource_type.name', 'Adamantita');
                                $oricalco = collect($resourcesByTier[3] ?? [])->firstWhere('resource_type.name', 'Oricalco');
                                $hasAdamantita = $adamantita && $adamantita['quantity'] >= ($nextSealCost['adamantita'] ?? 0);
                                $hasOricalco = $oricalco && $oricalco['quantity'] >= ($nextSealCost['oricalco'] ?? 0);
                                $canDeposit = $hasAdamantita && $hasOricalco;
                            @endphp
                            <span class="text-sm {{ $hasAdamantita ? 'text-green-400' : 'text-red-400' }}">
                                💠 {{ $adamantita['quantity'] ?? 0 }}/{{ $nextSealCost['adamantita'] ?? 0 }} Adamantita
                            </span>
                            <span class="text-sm {{ $hasOricalco ? 'text-green-400' : 'text-red-400' }}">
                                🔱 {{ $oricalco['quantity'] ?? 0 }}/{{ $nextSealCost['oricalco'] ?? 0 }} Oricalco
                            </span>
                        </div>
                    </div>

                    @if($canDeposit)
                        <button 
                            wire:click="depositEternalAlloy"
                            class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-bold text-lg transition-all shadow-lg hover:shadow-purple-500/50 hover:scale-105"
                        >
                            ⚡ Depositar Aleación
                        </button>
                    @else
                        <div class="px-8 py-4 bg-slate-700 text-slate-400 rounded-lg font-bold text-lg">
                            🔒 Recursos Insuficientes
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Flash message de feedback de hotkeys -->
        @if(session()->has('hotkey_message'))
            <div class="mb-4 bg-green-900/50 border border-green-600 text-green-300 px-4 py-2 rounded-lg text-center animate-pulse">
                {{ session('hotkey_message') }}
            </div>
        @endif

        <!-- Resources by Tier -->
        @foreach($resourcesByTier as $tier => $resources)
            <div class="mb-2">
                <h2 class="text-xl font-semibold text-center text-white mb-2">
                    @if($tier == 0)
                        Tier 0
                    @elseif($tier == 1)
                        Tier 1
                    @elseif($tier == 2)
                        Tier 2
                    @else
                        Tier 3
                    @endif
                </h2>
                
                <div class="grid grid-cols-4 gap-2">
                    @foreach($resources as $resource)
                    @php
                        $upgradeCost = pow(2, $resource['production_level']);
                        $canUpgrade = $resource['quantity'] >= $upgradeCost;
                        $maxStock = pow(2, $resource['production_level'] + 1);
                        
                        // NUEVA LÓGICA: Eficiencia basada en la instancia del recurso
                        $resourceModel = \App\Models\Resource::find($resource['id']);
                        $efficiencyPercent = $resourceModel ? $resourceModel->getEfficiency() : 1;
                        $efficiencyPercent = $efficiencyPercent * 100;
                        
                        $stockPercent = $maxStock > 0 ? round(($resource['quantity'] / $maxStock) * 100) : 0;
                        
                        // Calcular producción por día (solo Tier 0)
                        $perDay = 0;
                        if($resource['resource_type']['tier'] == 0) {
                            $multiplier = \App\Models\Upgrade::getGenerationMultiplier($resource['resource_type_id']);
                            $perTurn = $resource['production_level'] * $resource['resource_type']['passive_generation_base'] * $multiplier;
                            $perDay = $perTurn * $turnsPerDay;
                        }
                    @endphp
                        <div class="bg-slate-800/70 backdrop-blur rounded-lg p-2 border {{ $stockPercent >= 90 ? 'border-red-700' : ($stockPercent >= 70 ? 'border-yellow-700' : 'border-slate-700') }} hover:border-slate-600 transition-all grid grid-cols-2">
                            
                            <!-- Cantidad y Barra de Stock -->
                            <div class="text-center col-span-1 flex flex-col items-center justify-center">
                                <div class="font-semibold text-white text-xl mb-1">
                                    {{ number_format($resource['quantity']) }}
                                </div>
                                
                                <!-- Barra de progreso de stock -->
                                <div class="w-full bg-slate-700/50 rounded-full h-2 mb-1">
                                    <div 
                                        class="h-2 rounded-full transition-all {{ $stockPercent >= 90 ? 'bg-red-500' : ($stockPercent >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                        style="width: {{ min($stockPercent, 100) }}%"
                                    ></div>
                                </div>
                                
                                <div class="text-xs text-slate-400">
                                    {{ number_format($maxStock) }} máx - {{ $stockPercent }}%
                                </div>
                            </div>
                            
                            <!-- Información -->
                            <div class="text-center col-span-1">
                                <div class="text-center mb-1">
                                    <div class="font-semibold text-white text-sm">{{ $resource['resource_type']['name'] }}</div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-xs text-slate-400 mb-1">
                                        Nivel {{ $resource['production_level'] }}
                                    </div>
                                    
                                    @if($resource['resource_type']['tier'] == 0)
                                        <div class="text-xs text-blue-400 mb-1">
                                            @php
                                                $multiplier = \App\Models\Upgrade::getGenerationMultiplier($resource['resource_type_id']);
                                                $amount = $resource['production_level'] * $resource['resource_type']['passive_generation_base'] * $multiplier;
                                            @endphp
                                            +{{ $amount }}/turno
                                        </div>
                                        <div class="text-xs text-cyan-400 mb-1">
                                            ~{{ number_format($perDay, 1) }}/día
                                        </div>
                                    @else
                                        {{-- NUEVA VISUALIZACIÓN: Mostrar eficiencia mejorada --}}
                                        <div class="text-xs text-purple-400 mb-1">
                                            <span class="inline-block bg-purple-900/40 px-1.5 py-0.5 rounded">
                                                {{ $efficiencyPercent }}% efic.
                                            </span>
                                        </div>
                                        @if($efficiencyPercent > 100)
                                            <div class="text-[10px] text-green-400 mb-1">
                                                ~{{ round(100 / ($efficiencyPercent / 100)) }}% costo
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($canUpgrade)
                                        <button 
                                            wire:click="upgradeResource({{ $resource['id'] }})"
                                            class="w-full text-xs px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors"
                                        >
                                            Mejorar ({{ number_format($upgradeCost) }})
                                        </button>
                                    @else
                                        <div class="text-xs text-slate-500">
                                            Necesita {{ number_format($upgradeCost) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @php 
                                $recipe = \App\Models\Recipe::with(['outputResourceType', 'inputs.resourceType', 'unlockRequirements.resourceType'])
                                    ->where('output_resource_type_id', $resource['resource_type_id'])
                                    ->first();
                                
                                if ($recipe) {
                                    $outputResource = \App\Models\Resource::where('resource_type_id', $recipe->output_resource_type_id)->first();
                                    $recipe = [
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
                                            $resource = \App\Models\Resource::where('resource_type_id', $req->resource_type_id)->first();
                                            return [
                                                'name' => $req->resourceType->name,
                                                'icon' => $req->resourceType->icon,
                                                'required' => $req->quantity_required,
                                                'current' => $resource ? $resource->quantity : 0,
                                            ];
                                        })->toArray(),
                                        'can_unlock' => $recipe->canUnlock(),
                                        'production_level' => $outputResource ? $outputResource->production_level : 1,
                                        'efficiency' => $outputResource ? $recipe->getTotalEfficiency() : 1,
                                        'efficiency_percent' => $outputResource ? $recipe->getTotalEfficiencyPercent() : 100,
                                        'automation' => [
                                            'is_active' => $recipe->automation ? $recipe->automation->is_active : true,
                                            'percentage' => $recipe->automation ? $recipe->automation->production_percentage : 100,
                                        ]
                                    ];
                                }
                            @endphp
                            
                            @if($recipe)
                                @if(!$recipe['is_unlocked'])
                                    <!-- Receta Bloqueada -->
                                    <div class="col-span-2 mt-2 pt-2 border-t border-slate-700">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2 opacity-50">
                                                <span class="text-lg grayscale">{{ $resource['resource_type']['icon'] }}</span>
                                                <div>
                                                    <div class="font-semibold text-white text-xs">{{ $recipe['name'] }}</div>
                                                    <div class="text-xs text-red-400">🔒 Bloqueada</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Requisitos de Desbloqueo -->
                                        <div class="mb-2 text-xs bg-slate-900/50 rounded p-2">
                                            <div class="text-slate-400 mb-1 font-semibold">Requisitos:</div>
                                            <div class="space-y-1">
                                                @foreach($recipe['unlock_requirements'] as $req)
                                                    <div class="flex justify-between items-center {{ $req['current'] >= $req['required'] ? 'text-green-400' : 'text-slate-300' }}">
                                                        <span>{{ $req['icon'] }} {{ $req['name'] }}</span>
                                                        <span class="font-mono text-xs">
                                                            {{ number_format($req['current']) }} / {{ number_format($req['required']) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if($recipe['can_unlock'])
                                            <button 
                                                wire:click="unlockRecipe({{ $recipe['id'] }})"
                                                class="w-full px-2 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold transition-colors"
                                            >
                                                🔓 Desbloquear
                                            </button>
                                        @else
                                            <div class="text-center text-xs text-slate-500 py-1">
                                                Acumula recursos
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Receta Desbloqueada -->
                                    <div class="col-span-2 mt-2 pt-2 border-t border-slate-700">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-1">
                                                <span class="text-lg">{{ $recipe['output']['icon'] }}</span>
                                                <div>
                                                    <div class="font-semibold text-white text-xs">{{ $recipe['name'] }}</div>
                                                    {{-- MOSTRAR EFICIENCIA TOTAL --}}
                                                    @if($recipe['efficiency_percent'] > 100)
                                                        <div class="text-[10px] text-green-400">
                                                            ⚡ {{ $recipe['efficiency_percent'] }}% eficiente
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <button 
                                                wire:click="toggleRecipeAutomation({{ $recipe['id'] }})"
                                                class="px-2 py-0.5 rounded text-xs font-semibold transition-colors {{ $recipe['automation']['is_active'] ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-slate-600 hover:bg-slate-700 text-slate-300' }}"
                                            >
                                                {{ $recipe['automation']['is_active'] ? '▶' : '⸏' }}
                                            </button>
                                        </div>

                                        <!-- Inputs con eficiencia NUEVA -->
                                        <div class="mb-2 text-xs">
                                            <div class="text-slate-400 mb-1">Requiere:</div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($recipe['inputs'] as $input)
                                                    @php
                                                        // Calcular costo ajustado con nueva lógica
                                                        $efficiency = $recipe['efficiency'] ?? 1;
                                                        $adjustedCost = ceil($input['required'] / $efficiency);
                                                    @endphp
                                                    <span class="bg-slate-700/50 px-1.5 py-0.5 rounded text-xs text-slate-300">
                                                        {{ $input['icon'] }} 
                                                        <span class="font-bold {{ $adjustedCost < $input['required'] ? 'text-green-400' : '' }}">
                                                            {{ $adjustedCost }}
                                                        </span>
                                                        @if($adjustedCost < $input['required'])
                                                            <span class="text-slate-500 line-through text-[10px]">{{ $input['required'] }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Production Percentage -->
                                        <div class="text-xs">
                                            <div class="text-slate-400 mb-1">Producción: {{ $recipe['automation']['percentage'] }}%</div>
                                            <input 
                                                type="range" 
                                                min="10" 
                                                max="100" 
                                                step="10" 
                                                value="{{ $recipe['automation']['percentage'] }}"
                                                wire:change="updateRecipePercentage({{ $recipe['id'] }}, $event.target.value)"
                                                class="w-full h-1.5 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                                            >
                                            <div class="text-xs text-slate-500 mt-1">
                                                @php
                                                    $maxProdWithEfficiency = ceil($recipe['production_level'] * $recipe['efficiency'] * ($recipe['automation']['percentage'] / 100));
                                                @endphp
                                                Hasta {{ $maxProdWithEfficiency }}/turno
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="mt-8">
            <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                <span class="text-purple-400">⚡</span> Mercado de Mejoras
            </h2>
            
            @php
                // Agrupamos las mejoras por su categoría
                $upgradesByCategory = collect($upgrades)
                    /* ->where('is_purchased', false) */
                    ->groupBy('category');
                
                // Mapeo de nombres internos a títulos legibles
                $categoryTitles = [
                    'passive_generation' => 'Generación Elemental',
                    'turns' => 'Tiempo y Turnos',
                    'days' => 'Pactos Especiales',
                    'efficiency' => 'Eficiencia de Forja'
                ];
            @endphp

            @if($upgradesByCategory->isEmpty())
                <div class="bg-slate-800/50 rounded-lg p-6 text-center border border-slate-700">
                    <p class="text-slate-400">¡Has adquirido todas las mejoras disponibles por ahora!</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($upgradesByCategory as $category => $categoryUpgrades)
                        <div class="category-group">
                            <h3 class="text-lg font-semibold text-purple-300 mb-3 border-b border-purple-900/30 pb-1">
                                {{ $categoryTitles[$category] ?? ucfirst($category) }}
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($categoryUpgrades as $upgrade)
                                    <div class="bg-slate-800/70 backdrop-blur rounded-lg p-4 border {{ $upgrade['can_purchase'] ? 'border-purple-600/50 shadow-lg shadow-purple-900/10' : 'border-slate-700' }} flex flex-col justify-between transition-all hover:scale-[1.02]">
                                        
                                        <div>
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="font-bold text-white text-sm leading-tight">{{ $upgrade['name'] }}</div>
                                                @if($upgrade['can_purchase'])
                                                    <span class="flex h-2 w-2 rounded-full bg-purple-400 animate-pulse"></span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 leading-snug mb-3 italic">
                                                {{ $upgrade['description'] }}
                                            </p>

                                            @if($upgrade['requires_upgrade'])
                                                <div class="text-[10px] uppercase tracking-wider mb-2 font-bold {{ $upgrade['requires_upgrade']['is_purchased'] ? 'text-green-500' : 'text-amber-500' }}">
                                                    @if($upgrade['requires_upgrade']['is_purchased'])
                                                        ✓ {{ $upgrade['requires_upgrade']['name'] }}
                                                    @else
                                                        🔒 Req: {{ $upgrade['requires_upgrade']['name'] }}
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mb-4 bg-slate-900/60 rounded p-2 border border-slate-700/50">
                                                <div class="text-[10px] text-slate-500 uppercase font-bold mb-1">Costo Requerido:</div>
                                                <div class="space-y-1">
                                                    @foreach($upgrade['costs'] as $cost)
                                                        <div class="flex justify-between items-center {{ $cost['current'] >= $cost['required'] ? 'text-green-400' : 'text-slate-300' }}">
                                                            <span class="text-xs">{{ $cost['icon'] }} {{ $cost['name'] }}</span>
                                                            <span class="font-mono text-[11px]">
                                                                {{ number_format($cost['current']) }}/{{ number_format($cost['required']) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        @if($upgrade['is_purchased'])
                                            <div class="text-center text-[10px] text-slate-500 font-bold py-2 bg-green-600/30 rounded border border-dashed border-slate-700">
                                                ADQUIRIDO
                                            </div>
                                        @elseif($upgrade['can_purchase'])
                                            <button 
                                                wire:click="purchaseUpgrade({{ $upgrade['id'] }})"
                                                class="w-full py-2 bg-purple-600 hover:bg-purple-500 text-white rounded font-bold transition-all text-xs shadow-inner"
                                            >
                                                ADQUIRIR
                                            </button>
                                        @else
                                            <div class="text-center text-[10px] text-slate-500 font-bold py-2 bg-slate-900/30 rounded border border-dashed border-slate-700">
                                                @if($upgrade['requires_upgrade'] && !$upgrade['requires_upgrade']['is_purchased'])
                                                    BLOQUEADO
                                                @else
                                                    RECURSOS INSUFICIENTES
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="mt-8 bg-slate-800/50 backdrop-blur rounded-lg p-6 border border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-purple-400">📋 Estado del Juego</h2>
            </div>
            <pre class="bg-slate-900/80 p-4 rounded text-xs text-slate-300 overflow-x-auto font-mono whitespace-pre-wrap select-all">Día {{ $currentDay }}, Turnos {{ $turnsPerDay }}, Días máx {{ $maxDays }}

Turnos totales jugados: {{ \App\Models\GameState::first()->total_turns_played ?? 0 }}

TIER 0 - ELEMENTOS
@foreach($resourcesByTier[0] ?? [] as $resource)
@php
    $multiplier = \App\Models\Upgrade::getGenerationMultiplier($resource['resource_type_id']);
    $perTurn = $resource['production_level'] * $resource['resource_type']['passive_generation_base'] * $multiplier;
    $maxStock = pow(2, $resource['production_level'] + 1);
@endphp
{{ $resource['resource_type']['name'] }}: nivel {{ $resource['production_level'] }}, stock {{ $resource['quantity'] }}/{{ $maxStock }}, gen {{ number_format($perTurn, 2) }}/turno, mult {{ number_format($multiplier, 2) }}
@endforeach

TIER 1 - BÁSICOS
@foreach($resourcesByTier[1] ?? [] as $resource)
@php
    $efficiencyPercent = 100 + (($resource['production_level'] - 1) * 10);
    $recipe = \App\Models\Recipe::where('output_resource_type_id', $resource['resource_type_id'])->first();
    $upgradeReduction = \App\Models\Upgrade::getRecipeCostReduction($resource['resource_type_id']);
    $totalEfficiency = ($efficiencyPercent / 100) * (1 + $upgradeReduction);
    $maxStock = pow(2, $resource['production_level'] + 1);
    $unlocked = $recipe ? ($recipe->is_unlocked ? 'desbloqueado' : 'bloqueado') : 'sin-receta';
@endphp
{{ $resource['resource_type']['name'] }}: {{ $unlocked }}, nivel {{ $resource['production_level'] }}, stock {{ $resource['quantity'] }}/{{ $maxStock }}, efic {{ number_format($totalEfficiency * 100, 0) }}%
@endforeach

TIER 2 - AVANZADOS
@foreach($resourcesByTier[2] ?? [] as $resource)
@php
    $efficiencyPercent = 100 + (($resource['production_level'] - 1) * 10);
    $recipe = \App\Models\Recipe::where('output_resource_type_id', $resource['resource_type_id'])->first();
    $upgradeReduction = \App\Models\Upgrade::getRecipeCostReduction($resource['resource_type_id']);
    $totalEfficiency = ($efficiencyPercent / 100) * (1 + $upgradeReduction);
    $maxStock = pow(2, $resource['production_level'] + 1);
    $unlocked = $recipe ? ($recipe->is_unlocked ? 'desbloqueado' : 'bloqueado') : 'sin-receta';
@endphp
{{ $resource['resource_type']['name'] }}: {{ $unlocked }}, nivel {{ $resource['production_level'] }}, stock {{ $resource['quantity'] }}/{{ $maxStock }}, efic {{ number_format($totalEfficiency * 100, 0) }}%
@endforeach

TIER 3 - SUPERIORES
@foreach($resourcesByTier[3] ?? [] as $resource)
@php
    $efficiencyPercent = 100 + (($resource['production_level'] - 1) * 10);
    $recipe = \App\Models\Recipe::where('output_resource_type_id', $resource['resource_type_id'])->first();
    $upgradeReduction = \App\Models\Upgrade::getRecipeCostReduction($resource['resource_type_id']);
    $totalEfficiency = ($efficiencyPercent / 100) * (1 + $upgradeReduction);
    $maxStock = pow(2, $resource['production_level'] + 1);
    $unlocked = $recipe ? ($recipe->is_unlocked ? 'desbloqueado' : 'bloqueado') : 'sin-receta';
@endphp
{{ $resource['resource_type']['name'] }}: {{ $unlocked }}, nivel {{ $resource['production_level'] }}, stock {{ $resource['quantity'] }}/{{ $maxStock }}, efic {{ number_format($totalEfficiency * 100, 0) }}%
@endforeach

UPGRADES COMPRADOS
@php
    $purchasedUpgrades = collect($upgrades)->where('is_purchased', true);
@endphp
@forelse($purchasedUpgrades as $upgrade)
{{ $upgrade['code'] }}
@empty
ninguno
@endforelse
</pre>
        </div>
    </div>
    <!-- Hotkeys -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            document.addEventListener('keydown', (e) => {
                // Prevenir si está escribiendo en un input (aunque no hay inputs por ahora)
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }

                switch(e.key.toLowerCase()) {
                    case 'q':
                        @this.call('upgradeAllTier', 0);
                        break;
                    case 'w':
                        @this.call('upgradeAllTier', 1);
                        break;
                    case 'e':
                        @this.call('upgradeAllTier', 2);
                        break;
                    case 'r':
                        @this.call('upgradeAllTier', 3);
                        break;
                    case 's':
                        @this.call('toggleAllProduction');
                        break;
                    case '1':
                        @this.call('toggleProductionTier', 1);
                        break;
                    case '2':
                        @this.call('toggleProductionTier', 2);
                        break;
                    case '3':
                        @this.call('toggleProductionTier', 3);
                        break;
                    case 'm':
                        document.querySelector('.category-group')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        break;
                }
            });
        });
    </script>

    <!-- Indicador de Hotkeys -->
    <div class="fixed bottom-4 right-4 bg-slate-800/90 backdrop-blur border border-slate-700 rounded-lg p-3 text-xs text-slate-400 shadow-xl">
        <div class="font-bold text-slate-300 mb-2">⌨️ Atajos</div>
        <div class="space-y-1">
            <div><kbd class="bg-slate-700 px-1.5 py-0.5 rounded">Q/W/E/R</kbd> Mejorar Tier 0/1/2/3</div>
            <div><kbd class="bg-slate-700 px-1.5 py-0.5 rounded">S</kbd> Stop/Resume TODO</div>
            <div><kbd class="bg-slate-700 px-1.5 py-0.5 rounded">1/2/3</kbd> Stop/Resume Tier</div>
            <div><kbd class="bg-slate-700 px-1.5 py-0.5 rounded">M</kbd> Ir a Mejoras</div>
        </div>
    </div>
</div>