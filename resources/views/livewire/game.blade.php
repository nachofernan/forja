<div wire:poll.3s="nextTurn" class="min-h-screen bg-slate-950 text-slate-200 p-4 pb-24">
    <div class="max-w-6xl mx-auto">
        <header class="fixed top-0 inset-x-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 p-4">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex gap-8">
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold">Día</p>
                        <p class="text-2xl font-black text-amber-500 leading-none">{{ $currentDay }}/{{ $maxDays }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold">Turno</p>
                        <p class="text-2xl font-black text-blue-500 leading-none">{{ $currentTurnInDay }}/{{ $turnsPerDay }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if($dayComplete && !$gameOver)
                        <button wire:click="nextDay" class="bg-green-600 px-6 py-2 rounded-full font-bold animate-bounce shadow-lg shadow-green-900/40">Siguiente Día</button>
                    @elseif($gameOver)
                        <button wire:click="initializeGame" class="bg-red-600 px-6 py-2 rounded-full font-bold">Reiniciar</button>
                    @else
                        <div class="text-right">
                            <p class="text-[10px] uppercase text-slate-500 font-bold">Siguiente</p>
                            <p class="text-2xl font-mono font-bold text-pink-500">{{ $countdown }}s</p>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <div class="h-24"></div>

        <section class="mb-12 bg-indigo-950/20 border border-indigo-500/30 rounded-2xl p-6 shadow-2xl overflow-hidden relative">
            <div class="relative z-10">
                <h2 class="text-center text-2xl font-black text-indigo-300 mb-6 tracking-widest">⭐ SELLO PRIMORDIAL ⭐</h2>
                <div class="grid grid-cols-5 sm:grid-cols-10 gap-3 mb-8">
                    @for($i = 1; $i <= 10; $i++)
                        <div class="aspect-square rounded-xl border-2 flex items-center justify-center transition-all {{ $i <= $sealProgress ? 'bg-amber-500 border-amber-300 shadow-[0_0_15px_rgba(245,158,11,0.5)]' : 'bg-slate-900 border-slate-700 opacity-40' }}">
                            @if($i <= $sealProgress) <span class="text-2xl">⭐</span> @else <span class="text-slate-600 font-bold">{{ $i }}</span> @endif
                        </div>
                    @endfor
                </div>

                @if(!$isVictory)
                <div class="flex flex-col md:flex-row items-center justify-between bg-slate-900/50 p-4 rounded-xl gap-4">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">Costo Unidad #{{ $nextSealCost['unit_number'] }}</p>
                        <div class="flex gap-4">
                            <span class="font-mono text-sm">💠 {{ $nextSealCost['adamantita'] }} Adamantita</span>
                            <span class="font-mono text-sm">🔱 {{ $nextSealCost['oricalco'] }} Oricalco</span>
                        </div>
                    </div>
                    <button wire:click="depositEternalAlloy" wire:loading.attr="disabled" class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-700 rounded-lg font-black transition-all transform active:scale-95 shadow-lg">
                        <span wire:loading.remove>⚡ DEPOSITAR ALEACIÓN</span>
                        <span wire:loading>PROCESANDO...</span>
                    </button>
                </div>
                @endif
            </div>
        </section>

        @foreach($resourcesByTier as $tier => $resources)
            <h3 class="text-slate-500 font-black text-sm uppercase mb-4 tracking-tighter">Tier {{ $tier }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
                @foreach($resources as $res)
                <div class="bg-slate-900 border {{ $res['stock_percent'] > 90 ? 'border-red-500/50' : 'border-slate-800' }} rounded-xl p-4 transition-all hover:bg-slate-800/50">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-3xl">{{ $res['icon'] }}</span>
                        <div class="text-right">
                            <p class="text-xl font-black {{ $res['stock_percent'] > 90 ? 'text-red-400' : 'text-white' }}">{{ number_format($res['quantity']) }}</p>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Max: {{ number_format($res['max_stock']) }}</p>
                        </div>
                    </div>

                    <div class="w-full bg-slate-950 h-1.5 rounded-full mb-4 overflow-hidden">
                        <div class="h-full transition-all duration-500 {{ $res['stock_percent'] > 90 ? 'bg-red-500' : 'bg-blue-500' }}" style="width: {{ $res['stock_percent'] }}%"></div>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Nv. {{ $res['level'] }}</span>
                        @if($res['can_upgrade'])
                            <button wire:click="upgradeResource({{ $res['id'] }})" class="text-[10px] bg-green-600/20 text-green-400 border border-green-600/50 px-2 py-1 rounded hover:bg-green-600 hover:text-white transition-all">Mejorar ({{ number_format($res['upgrade_cost']) }})</button>
                        @endif
                    </div>

                    @if($res['recipe'])
                        <div class="border-t border-slate-800 pt-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-bold text-indigo-400 uppercase">Producción</span>
                                <button wire:click="toggleRecipeAutomation({{ $res['recipe']['id'] }})" class="p-1 rounded {{ $res['recipe']['is_active'] ? 'text-green-500' : 'text-slate-600' }}">
                                    {!! $res['recipe']['is_active'] ? '▶' : '⏸' !!}
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($res['recipe']['inputs'] as $in)
                                    <span class="text-[10px] bg-slate-950 px-1.5 py-0.5 rounded">{{ $in['icon'] }} {{ $in['cost'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    @if(session()->has('hotkey_message'))
        <div class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-6 py-2 rounded-full shadow-2xl animate-bounce z-50">
            {{ session('hotkey_message') }}
        </div>
    @endif
</div>