<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Dashboard</h2>
            <a href="{{ route('dashboard.exportar', ['desde' => $desde->format('Y-m-d'), 'hasta' => $hasta->format('Y-m-d')]) }}"
                class="btn btn-success btn-sm">
                ↓ Exportar Excel
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- ── CARD TOTAL ALMACÉN ── --}}
    <div class="card bg-primary text-primary-content shadow mb-6">
        <div class="card-body flex-row items-center justify-between">
            <div>
                <p class="text-primary-content/70 text-sm uppercase tracking-wide">Total en almacén</p>
                <p class="text-4xl font-bold">{{ number_format($totalAlmacen) }}</p>
                <p class="text-primary-content/60 text-sm mt-1">artículos disponibles · {{ number_format($totalArticulos) }} registros totales</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
    </div>

    {{-- ── SELECTOR DE PERIODO ── --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-3">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="form-control">
                    <label class="label py-1"><span class="label-text text-xs font-medium">Desde</span></label>
                    <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                        class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text text-xs font-medium">Hasta</span></label>
                    <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                        class="input input-bordered input-sm" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                <div class="divider divider-horizontal mx-0"></div>
                <span class="text-xs text-base-content/40 self-center">Accesos rápidos:</span>
                @foreach([
                    ['label' => '1 mes',  'meses' => 1],
                    ['label' => '3 meses', 'meses' => 3],
                    ['label' => '6 meses', 'meses' => 6],
                    ['label' => '9 meses', 'meses' => 9],
                ] as $preset)
                    <a href="{{ request()->fullUrlWithQuery([
                        'desde' => now()->subMonths($preset['meses'])->format('Y-m-d'),
                        'hasta' => now()->format('Y-m-d'),
                    ]) }}" class="btn btn-ghost btn-sm">{{ $preset['label'] }}</a>
                @endforeach
            </form>
        </div>
    </div>

    {{-- ── CARDS MÉTRICAS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-xs text-base-content/50 uppercase tracking-wide">Solicitudes</p>
                <p class="text-3xl font-bold text-info">{{ number_format($totalSolicitudes) }}</p>
                <p class="text-sm text-base-content/60">{{ number_format($totalArticulosSolicitud) }} artículos enviados</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-xs text-base-content/50 uppercase tracking-wide">Órdenes de compra</p>
                <p class="text-3xl font-bold text-success">{{ number_format($totalOrdenes) }}</p>
                <p class="text-sm text-base-content/60">{{ number_format($totalArticulosOrdenes) }} artículos recibidos</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-xs text-base-content/50 uppercase tracking-wide">Importe solicitudes</p>
                <p class="text-3xl font-bold text-warning">
                    ${{ number_format($importeSolicitudes, 2) }}
                </p>
                <p class="text-sm text-base-content/60">facturado en el periodo</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-xs text-base-content/50 uppercase tracking-wide">Importe órdenes</p>
                <p class="text-3xl font-bold text-accent">
                    ${{ number_format($importeOrdenes, 2) }}
                </p>
                <p class="text-sm text-base-content/60">compras en el periodo</p>
            </div>
        </div>
    </div>
    {{-- ── CALENDARIO DE EXÁMENES ── --}}
    <div class="card bg-base-100 shadow mb-6" x-data="calendarioWidget">
        <div class="card-body">

            {{-- Cabecera con navegación --}}
            <div class="flex items-center justify-between mb-4">
                <button @click="mesAnterior()" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-chevron-left class="w-4 h-4" />
                </button>
                <h3 class="font-semibold" x-text="mesNombre"></h3>
                <button @click="mesSiguiente()" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                </button>
            </div>

            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 mb-1">
                @foreach(['Lu','Ma','Mi','Ju','Vi','Sa','Do'] as $d)
                <div class="text-center text-xs font-medium text-base-content/40 py-1">{{ $d }}</div>
                @endforeach
            </div>

            {{-- Celdas del mes --}}
            <div class="grid grid-cols-7 gap-1" x-ref="grid">
                <template x-for="celda in celdas" :key="celda.key">
                    <div
                        class="relative min-h-[2.5rem] rounded-lg flex flex-col items-center pt-1 cursor-default"
                        :class="{
                            'opacity-25': !celda.delMes,
                            'bg-primary/10 ring-1 ring-primary': celda.esHoy,
                            'hover:bg-base-200': celda.delMes && !celda.esHoy,
                        }">

                        <span class="text-xs" :class="celda.esHoy ? 'font-bold text-primary' : 'text-base-content/70'"
                            x-text="celda.dia"></span>

                        <template x-if="celda.examenes && celda.examenes.length">
                            <div class="mt-0.5 w-full px-0.5">
                                <div
                                    x-data="{ open: false }"
                                    @click.stop="open = !open"
                                    class="relative">
                                    <div class="badge badge-primary badge-xs w-full cursor-pointer"
                                        x-text="celda.examenes.length + (celda.examenes.length === 1 ? ' examen' : ' exámenes')">
                                    </div>
                                    {{-- Popover --}}
                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition
                                        class="absolute z-50 left-1/2 -translate-x-1/2 top-5 w-56 bg-base-100 shadow-xl rounded-xl border border-base-200 p-3 text-left"
                                        style="display:none">
                                        <template x-for="ex in celda.examenes" :key="ex.ID">
                                            <div class="py-1 border-b border-base-200 last:border-0">
                                                <p class="text-xs font-semibold truncate" x-text="ex.EXAMEN"></p>
                                                <p class="text-xs text-base-content/50"
                                                    x-text="(ex.solicitud?.empresa?.nombre ?? '—') + ' · ' + ex.CANTIDAD + ' cands.'">
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

        </div>
    </div>

    {{-- ── GRÁFICA ── --}}
    <div class="card bg-base-100 shadow mb-6 overflow-hidden">
        <div class="card-body overflow-hidden">
            <h3 class="font-semibold mb-4">
                Actividad por {{ $agruparPor === 'week' ? 'semana' : 'día' }}
            </h3>
            <div class="relative w-full min-w-0" style="height: 200px;">
                <canvas id="grafica-actividad" style="max-width:100%"></canvas>
            </div>
        </div>
    </div>

    {{-- ── TOP EMPRESAS Y EXÁMENES ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Top empresas con solicitudes</h3>
                @forelse($topEmpresas as $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-base-200 last:border-0">
                        <span class="text-sm">{{ $item->empresa?->nombre ?? '—' }}</span>
                        <span class="badge badge-info badge-sm">{{ $item->total }} solicitudes</span>
                    </div>
                @empty
                    <p class="text-sm text-base-content/40 text-center py-4">Sin datos en el periodo.</p>
                @endforelse
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Top tipos de examen</h3>
                @forelse($topExamenes as $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-base-200 last:border-0">
                        <span class="text-sm">{{ $item->EXAMEN }}</span>
                        <div class="flex gap-2">
                            <span class="badge badge-ghost badge-sm">{{ $item->total }} sesiones</span>
                            <span class="badge badge-primary badge-sm">{{ number_format($item->candidatos) }} cands.</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-base-content/40 text-center py-4">Sin datos en el periodo.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── ALERTAS / MÉTRICAS EXTRA ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="card shadow {{ $articulosPerdidos > 0 ? 'bg-error text-error-content' : 'bg-base-100' }}">
            <div class="card-body">
                <p class="text-xs uppercase tracking-wide opacity-60">Artículos perdidos</p>
                <p class="text-3xl font-bold">{{ number_format($articulosPerdidos) }}</p>
                <p class="text-sm opacity-60">en el periodo seleccionado</p>
            </div>
        </div>

        <div class="card shadow {{ $articulosDestruccion > 0 ? 'bg-warning text-warning-content' : 'bg-base-100' }}">
            <div class="card-body">
                <p class="text-xs uppercase tracking-wide opacity-60">En cajas de destrucción</p>
                <p class="text-3xl font-bold">{{ number_format($articulosDestruccion) }}</p>
                <p class="text-sm opacity-60">artículos pendientes</p>
            </div>
        </div>

        <div class="card shadow {{ $solicitudesVencidas > 0 ? 'bg-error text-error-content' : 'bg-base-100' }}">
            <div class="card-body">
                <p class="text-xs uppercase tracking-wide opacity-60">Solicitudes vencidas</p>
                <p class="text-3xl font-bold">{{ number_format($solicitudesVencidas) }}</p>
                <p class="text-sm opacity-60">enviadas con exámenes ya pasados</p>
            </div>
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const solicitudesData = @json($solicitudesPorPeriodo->map(fn($r) => ['x' => $r->fecha, 'y' => $r->total]));
        const ordenesData     = @json($ordenesPorPeriodo->map(fn($r) => ['x' => $r->fecha, 'y' => $r->total]));

        const ctx = document.getElementById('grafica-actividad').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Solicitudes',
                        data: solicitudesData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Órdenes de compra',
                        data: ordenesData,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: {
                        type: 'category',
                        title: { display: false },
                        ticks: { maxTicksLimit: 12 },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index' },
                }
            }
        });

        const _mesInicial = @json($mesActual);
        const _examenesIniciales = @json($examenesCalendario);

        document.addEventListener('alpine:init', () => {
            Alpine.data('calendarioWidget', () => ({
                mes: _mesInicial,
                mesNombre: '',
                examenesPorFecha: {},
                celdas: [],
                cargando: false,

                init() {
                    this.examenesPorFecha = _examenesIniciales;
                    this.construir();
                },

                construir() {
                    const [anio, m] = this.mes.split('-').map(Number);
                    const primerDia = new Date(anio, m - 1, 1);
                    const ultimoDia = new Date(anio, m, 0);
                    const hoy = new Date();

                    this.mesNombre = primerDia.toLocaleDateString('es-MX', { month: 'long', year: 'numeric' })
                        .replace(/^\w/, c => c.toUpperCase());

                    let offset = primerDia.getDay();
                    offset = offset === 0 ? 6 : offset - 1;

                    const celdas = [];

                    for (let i = 0; i < offset; i++) {
                        const d = new Date(anio, m - 1, -offset + i + 1);
                        celdas.push({ key: 'pre-' + i, dia: d.getDate(), delMes: false, esHoy: false, examenes: [] });
                    }

                    for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                        const fecha = `${anio}-${String(m).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
                        const esHoy = hoy.getFullYear() === anio && hoy.getMonth() + 1 === m && hoy.getDate() === dia;
                        const examenes = this.examenesPorFecha[fecha] ?? [];
                        celdas.push({ key: fecha, dia, delMes: true, esHoy, examenes });
                    }

                    const resto = (7 - (celdas.length % 7)) % 7;
                    for (let i = 1; i <= resto; i++) {
                        celdas.push({ key: 'post-' + i, dia: i, delMes: false, esHoy: false, examenes: [] });
                    }

                    this.celdas = celdas;
                },

                async mesAnterior() {
                    const [anio, m] = this.mes.split('-').map(Number);
                    const nuevo = new Date(anio, m - 2, 1);
                    this.mes = `${nuevo.getFullYear()}-${String(nuevo.getMonth()+1).padStart(2,'0')}`;
                    await this.cargarMes();
                },

                async mesSiguiente() {
                    const [anio, m] = this.mes.split('-').map(Number);
                    const nuevo = new Date(anio, m, 1);
                    this.mes = `${nuevo.getFullYear()}-${String(nuevo.getMonth()+1).padStart(2,'0')}`;
                    await this.cargarMes();
                },

                async cargarMes() {
                    this.cargando = true;
                    try {
                        const res = await fetch(`/dashboard/calendario?mes=${this.mes}`);
                        const data = await res.json();
                        this.examenesPorFecha = data.examenes;
                    } catch(e) {
                        console.error('Error cargando calendario', e);
                    } finally {
                        this.cargando = false;
                        this.construir();
                    }
                }
            }));
        });
    </script>

</x-app-layout>