<div wire:poll.1s="nextTurn" class="min-h-screen bg-slate-950 text-slate-200 p-4">
    <header class="fixed top-0 inset-x-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Progreso Sello</p>
                    <div class="flex gap-1 mt-1">
                        @for($i=1; $i<=10; $i++)
                            <div class="w-3 h-3 rounded-full {{ $i <= $sealProgress ? 'bg-amber-400 shadow-[0_0_8px_#fbbf24]' : 'bg-slate-700' }}"></div>
                        @endfor
                    </div>
                </div>
                <div class="border-l border-slate-700 pl-6">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Día {{ $currentDay }}/{{ $maxDays }}</p>
                    <p class="text-xl font-black text-blue-400 leading-none">{{ $currentTurnInDay }}/{{ $turnsPerDay }} <span class="text-xs font-normal text-slate-500">turnos</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right mr-4">
                    <p class="text-[10px] text-slate-500 font-bold uppercase italic">Sincronizando</p>
                    <p class="text-lg font-mono font-bold text-pink-500">{{ $countdown }}s</p>
                </div>
                @if($dayComplete)
                    <button wire:click="nextDay" class="bg-green-600 px-6 py-2 rounded-lg font-bold hover:scale-105 transition-transform shadow-lg shadow-green-900/20">Cerrar Día</button>
                @endif
            </div>
        </div>
    </header>

    <div class="h-28"></div>

    <main class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-8">
            @foreach($resourcesByTier as $tier => $resources)
                <section>
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                        <span class="w-8 h-[1px] bg-slate-800"></span> Tier {{ $tier }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($resources as $res)
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
                                <div class="flex justify-between items-start relative z-10">
                                    <div class="flex gap-3">
                                        <span class="text-4xl filter drop-shadow-md">{{ $res['icon'] }}</span>
                                        <div>
                                            <h4 class="font-bold text-slate-300">{{ $res['name'] }}</h4>
                                            <p class="text-[10px] text-slate-500 uppercase">Eficiencia: <span class="text-green-400">{{ $res['eff'] }}%</span></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-mono font-black text-white">{{ number_format($res['qty']) }}</p>
                                        <p class="text-[10px] text-slate-500">CAP: {{ number_format($res['max']) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 h-1 w-full bg-slate-950 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: {{ ($res['qty']/$res['max'])*100 }}%"></div>
                                </div>

                                <div class="mt-4 flex justify-between items-center">
                                    <button wire:click="upgradeResource({{ $res['id'] }})" @disabled(!$res['can_upgrade']) class="text-[10px] font-bold px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 disabled:opacity-30 transition-all">
                                        MEJORAR NV.{{ $res['level'] }} <span class="text-amber-500 ml-1">({{ $res['upgrade_cost'] }})</span>
                                    </button>
                                    
                                    @if($res['recipe'])
                                        <button wire:click="toggleRecipeAutomation({{ $res['recipe']['id'] }})" class="flex items-center gap-2 text-[10px] font-bold {{ $res['recipe']['active'] ? 'text-green-400' : 'text-slate-500' }}">
                                            {{ $res['recipe']['active'] ? 'PRODUCIENDO' : 'PAUSADO' }}
                                            <span class="text-xs">{{ $res['recipe']['active'] ? '🟢' : '⚪' }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-slate-900 border border-indigo-500/20 rounded-2xl p-5 sticky top-28">
                <h3 class="text-indigo-400 font-black text-sm uppercase mb-6 flex items-center gap-2">
                    ✨ Mejoras del Reino
                </h3>
                
                @foreach($upgradesByCategory as $cat => $upgrades)
                    <div class="mb-8">
                        <p class="text-[10px] text-slate-500 font-bold uppercase mb-3">{{ $cat }}</p>
                        <div class="space-y-3">
                            @foreach($upgrades as $upg)
                                <div class="p-3 rounded-lg border {{ $upg['is_purchased'] ? 'border-green-500/30 bg-green-500/5' : 'border-slate-800 bg-slate-950/50' }}">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold {{ $upg['is_purchased'] ? 'text-green-400' : 'text-slate-300' }}">{{ $upg['name'] }}</p>
                                        @if($upg['is_purchased'])
                                            <span class="text-[10px] text-green-500 font-black">ADQUIRIDO</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1 mb-2">{{ $upg['description'] }}</p>
                                    
                                    @if(!$upg['is_purchased'])
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($upg['costs'] as $cost)
                                                <span class="text-[9px] px-1.5 py-0.5 bg-slate-900 rounded border {{ $cost['has'] >= $cost['qty'] ? 'border-slate-700' : 'border-red-900/50 text-red-400' }}">
                                                    {{ $cost['icon'] }} {{ $cost['qty'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                        <button wire:click="buyUpgrade({{ $upg['id'] }})" @disabled(!$upg['can_purchase']) class="w-full py-1.5 rounded text-[10px] font-black uppercase tracking-wider transition-all {{ $upg['can_purchase'] ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-slate-800 text-slate-600' }}">
                                            Comprar
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </main>
</div>