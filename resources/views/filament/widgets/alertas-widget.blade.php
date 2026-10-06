<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-2 mb-4">
            <x-heroicon-o-bell-alert class="h-6 w-6 text-amber-500" />
            <h2 class="text-lg font-bold">Centro de Alertas</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <x-heroicon-o-arrow-trending-down class="h-4 w-4 text-red-500" />
                    Stock Bajo ({{ $alertasStock->count() }})
                </h3>
                @if($alertasStock->count() > 0)
                    <div class="space-y-2">
                        @foreach($alertasStock as $mp)
                            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $mp->nombre }}</p>
                                        <p class="text-xs text-gray-600">{{ $mp->codigo_interno }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-red-600">
                                            {{ $mp->stock_actual }} {{ $mp->unidad_medida }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Mín: {{ $mp->stock_minimo }} {{ $mp->unidad_medida }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-green-50 border border-green-200 rounded text-center">
                        <p class="text-green-700 text-sm">✅ Todo el stock está en niveles óptimos</p>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <x-heroicon-o-clock class="h-4 w-4 text-amber-500" />
                    Lotes Próximos a Caducar (90 días)
                </h3>
                @if($alertasCaducidad->count() > 0)
                    <div class="space-y-2">
                        @foreach($alertasCaducidad as $lote)
                            @php
                                $diasRestantes = now()->diffInDays($lote->fecha_caducidad, false);
                                $colorClass = $diasRestantes <= 30 ? 'border-red-500 bg-red-50' : 'border-amber-500 bg-amber-50';
                            @endphp
                            <div class="p-3 {{ $colorClass }} border-l-4 rounded">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $lote->numero_lote }}</p>
                                        <p class="text-xs text-gray-600">
                                            {{ $lote->productoTerminado->nombre ?? 'Producto no disponible' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $lote->fecha_caducidad->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs {{ $diasRestantes <= 30 ? 'text-red-600' : 'text-amber-600' }}">
                                            {{ $diasRestantes }} días restantes
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-green-50 border border-green-200 rounded text-center">
                        <p class="text-green-700 text-sm">✅ No hay lotes próximos a caducar</p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
