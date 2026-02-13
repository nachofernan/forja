<div wire:poll.1s="nextTurn" class="min-h-screen bg-slate-950 text-slate-200 p-4">
    <header class="fixed top-0 inset-x-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 p-3">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <div>
                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest">Estado del Reino</p>
                    <p class="text-lg font-black text-amber-500 leading-none">DÍA {{ $currentDay }} <span class="text-slate-600 text-xs font-normal">/ {{ $maxDays }}</span></p>
                </div>
                <div class="h-8 w-[1px] bg-slate-800"></div>
                <div>
                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest">Ciclo de Turnos</p>
                    <p class="text-lg font-black text-blue-400 leading-none">{{ $currentTurnInDay }} <span class="text-slate-600 text-xs font-normal">/ {{ $turnsPerDay }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="bg-slate-950 px-4 py-1 rounded-full border border-pink-500/30">
                    <span class="text-[10px] text-pink-500 font-mono font-bold uppercase mr-2">Próximo Turno:</span>
                    <span class="text-lg font-mono font-black text-pink-500">{{ $countdown }}s</span>
                </div>
                @if($dayComplete)
                    <button wire:click="nextDay" class="bg-green-600 hover:bg-green-500 px-6 py-2 rounded-lg font-black text-sm transition-all animate-pulse shadow-lg shadow-green-900/20">PASAR DÍA</button>
                @endif
            </div>
        </div>
    </header>

    <div class="h-24"></div>

    <main class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-10">
            @foreach($resourcesByTier as $tier => $resources)
                <section>
                    <div class="flex items-center gap-4 mb-4">
                        <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.3em]">Tier {{ $tier }}</h3>
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-slate-800 to-transparent"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($resources as $res)
                            <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-5 hover:border-indigo-500/40 transition-all group">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex gap-4">
                                        <div class="text-4xl bg-slate-950 w-16 h-16 flex items-center justify-center rounded-2xl border border-slate-800 group-hover:scale-110 transition-transform">
                                            {{ $res['icon'] }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-slate-100 text-lg uppercase tracking-tight">{{ $res['name'] }}</h4>
                                            <p class="text-[10px] text-green-400 font-bold">EFICIENCIA: {{ $res['eff'] }}%</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-mono font-black {{ $res['qty'] >= $res['max'] ? 'text-red-500' : 'text-white' }}">
                                            {{ number_format($res['qty']) }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 font-bold">LÍMITE: {{ number_format($res['max']) }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="h-1.5 w-full bg-slate-950 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 shadow-[0_0_8px_#6366f1] transition-all duration-1000" style="width: {{ min(100, ($res['qty']/$res['max'])*100) }}%"></div>
                                    </div>

                                    <div class="flex justify-between items-center pt-2">
                                        <button wire:click="upgradeResource({{ $res['id'] }})" @disabled(!$res['can_upgrade']) class="flex flex-col items-start px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-750 disabled:opacity-20 transition-all border border-slate-700">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase">Mejorar Nivel {{ $res['level'] }}</span>
                                            <span class="text-xs font-black text-amber-500">💰 {{ number_format($res['upgrade_cost']) }}</span>
                                        </button>

                                        @if($res['recipe'])
                                            <button wire:click="toggleRecipeAutomation({{ $res['recipe']['id'] }})" class="group/btn flex items-center gap-3 px-4 py-2 rounded-xl border {{ $res['recipe']['active'] ? 'border-green-500/30 bg-green-500/5' : 'border-slate-800' }}">
                                                <div class="text-right">
                                                    <p class="text-[9px] font-bold uppercase {{ $res['recipe']['active'] ? 'text-green-400' : 'text-slate-500' }}">
                                                        {{ $res['recipe']['active'] ? 'Produciendo' : 'En Pausa' }}
                                                    </p>
                                                    <div class="flex gap-1 justify-end">
                                                        @foreach($res['recipe']['inputs'] as $in)
                                                            <span class="text-[10px] opacity-70">{{ $in['icon'] }}{{ $in['qty'] }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <span class="text-lg">{{ $res['recipe']['active'] ? '⏸' : '▶' }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <aside class="lg:col-span-4">
            <div class="sticky top-24 space-y-6">
                <div class="bg-indigo-950/30 border border-indigo-500/30 rounded-3xl p-6 shadow-2xl">
                    <h3 class="text-center text-indigo-300 font-black text-sm tracking-[0.3em] uppercase mb-4">Sello Primordial</h3>
                    <div class="grid grid-cols-5 gap-2 mb-6">
                        @for($i=1; $i<=10; $i++)
                            <div class="aspect-square rounded-lg border-2 flex items-center justify-center {{ $i <= $sealProgress ? 'bg-amber-500 border-amber-300 shadow-[0_0_10px_#f59e0b]' : 'bg-slate-900 border-slate-800 opacity-30' }}">
                                @if($i <= $sealProgress) ⭐ @else <span class="text-[10px] font-black text-slate-500">{{ $i }}</span> @endif
                            </div>
                        @endfor
                    </div>
                    <button wire:click="depositEternalAlloy" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg transition-all active:scale-95">
                        Depositar Aleación
                    </button>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 h-[600px] overflow-y-auto custom-scrollbar">
                    <h3 class="text-slate-400 font-black text-xs uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="text-lg">✨</span> Biblioteca Arcanista
                    </h3>

                    @foreach($upgradesByCategory as $category => $upgrades)
                        <div class="mb-8">
                            <p class="text-[10px] text-indigo-400 font-black uppercase mb-3 ml-1">{{ $category }}</p>
                            <div class="space-y-3">
                                @foreach($upgrades as $upg)
                                    <div class="p-4 rounded-2xl border transition-all {{ $upg['is_purchased'] ? 'bg-green-500/5 border-green-500/20' : 'bg-slate-950 border-slate-800' }}">
                                        <div class="flex justify-between items-start gap-2">
                                            <h5 class="text-xs font-black {{ $upg['is_purchased'] ? 'text-green-400' : 'text-slate-200' }} uppercase">{{ $upg['name'] }}</h5>
                                            @if($upg['is_purchased'])
                                                <span class="text-[9px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full font-bold">OK</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-500 my-2 leading-relaxed">{{ $upg['description'] }}</p>
                                        
                                        @if(!$upg['is_purchased'])
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                @foreach($upg['costs'] as $cost)
                                                    <span class="text-[10px] font-bold {{ $cost['has'] >= $cost['qty'] ? 'text-slate-400' : 'text-red-500' }}">
                                                        {{ $cost['icon'] }} {{ $cost['qty'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <button wire:click="buyUpgrade({{ $upg['id'] }})" @disabled(!$upg['can_purchase']) class="w-full py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter transition-all {{ $upg['can_purchase'] ? 'bg-indigo-600 hover:bg-indigo-500 text-white' : 'bg-slate-800 text-slate-600 cursor-not-allowed' }}">
                                                {{ $upg['can_purchase'] ? 'Adquirir Mejora' : 'Faltan Recursos' }}
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </main>
</div>