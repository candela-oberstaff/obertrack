<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profesionales - Dashboard Superadmin') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @include('admin.partials.nav')

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="p-4 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Listado de Profesionales</h3>
                        @if(isset($selectedCompany))
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-3 py-1 bg-[#22A9C8]/10 text-[#22A9C8] text-[10px] md:text-xs font-bold rounded-full">Filtrado por: {{ $selectedCompany->company_name ?? $selectedCompany->name }}</span>
                                <a href="{{ route('admin.professionals') }}" class="text-[10px] text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">Quitar filtro</a>
                            </div>
                        @endif
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-400">{{ $professionals->total() }} registros encontrados</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                <th class="px-4 md:px-8 py-4">Profesional</th>
                                <th class="px-4 md:px-8 py-4 hidden lg:table-cell">Relación Actual</th>
                                <th class="px-4 md:px-8 py-4 hidden sm:table-cell">Estado / Actividad</th>
                                <th class="px-4 md:px-8 py-4 hidden md:table-cell">Asignar a Empresa</th>
                                <th class="px-4 md:px-8 py-4 text-right">Reportes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($professionals as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 md:px-8 py-7 hover:bg-gray-50/80 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <x-user-avatar :user="$p['user']" size="10" class="md:w-12 md:h-12 shadow-sm border border-gray-100" />
                                            <div>
                                                <div class="font-bold text-gray-900 text-base leading-tight">{{ $p['user']->name }}</div>
                                                <div class="text-[10px] text-gray-400 md:hidden mt-0.5">{{ $p['status'] === 'red' ? 'Inactivo' : ($p['status'] === 'yellow' ? 'Alerta' : 'Activo') }}</div>
                                                <div class="text-[11px] text-gray-500 hidden md:block mt-0.5">{{ $p['user']->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-8 py-7 hidden lg:table-cell">
                                        @if($p['user']->empresa)
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-800">{{ $p['user']->empresa->company_name ?? $p['user']->empresa->name }}</span>
                                                <form action="{{ route('admin.unlink-professional', $p['user']->id) }}" method="POST" onsubmit="return confirmFormSubmit(event, '¿Estás seguro de desvincular este profesional?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-bold uppercase tracking-wider mt-1.5 transition-colors">Desvincular</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[11px] font-bold text-gray-300 uppercase italic tracking-widest">Sin empresa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-8 py-7 hidden sm:table-cell">
                                        <div class="flex flex-col gap-2.5">
                                            @if($p['status'] === 'red')
                                                <div class="flex items-center gap-2 relative group cursor-help">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-50 text-red-700 border border-red-100 font-extrabold text-[10px] uppercase tracking-wide">
                                                        <i class="bi bi-exclamation-octagon-fill mr-1"></i>
                                                        Inactivo (2+ días)
                                                    </span>
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                                                bg-gray-900 text-white text-[10px] rounded-lg
                                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                                                whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                                        Profesional con más de 48 horas sin registros
                                                    </div>
                                                </div>
                                            @elseif($p['status'] === 'yellow')
                                                <div class="flex items-center gap-2 relative group cursor-help">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-100 font-extrabold text-[10px] uppercase tracking-wide">
                                                        <i class="bi bi-clock-history mr-1"></i>
                                                        Alerta (1 día)
                                                    </span>
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                                                bg-gray-900 text-white text-[10px] rounded-lg
                                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                                                whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                                        Profesional con 24 horas sin registrar actividad
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2 relative group cursor-help">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-100 font-extrabold text-[10px] uppercase tracking-wide">
                                                        <i class="bi bi-check-circle-fill mr-1"></i>
                                                        Activo
                                                    </span>
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                                                bg-gray-900 text-white text-[10px] rounded-lg
                                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                                                whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                                        Profesional al día con sus registros
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="flex items-center gap-1.5 text-gray-400 relative group cursor-help">
                                                <i class="bi bi-calendar3 text-[10px]"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-tighter">
                                                    Visto: <span class="text-gray-500">{{ $p['last_registration'] ? \Carbon\Carbon::parse($p['last_registration'])->format('d/m/Y') : 'Nunca' }}</span>
                                                </span>
                                                <!-- Tooltip -->
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                                            bg-gray-900 text-white text-[10px] rounded-lg
                                                            opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                                            whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                                    Última fecha en la que el usuario registró horas o ausencias
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-8 py-5 hidden md:table-cell">
                                        <form action="{{ route('admin.assign-professional') }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="professional_id" value="{{ $p['user']->id }}">
                                            <select name="company_id" class="text-xs border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-600 bg-gray-50/50">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ $p['user']->empleador_id == $company->id ? 'selected' : '' }}>
                                                        {{ $company->company_name ?? $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="p-2 bg-[#22A9C8] text-white rounded-lg hover:opacity-90 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 md:px-8 py-7 text-right">
                                        <a href="{{ route('admin.professionals.show-details', $p['user']->id) }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-[#22A9C8] text-[#22A9C8] rounded-full text-[9px] md:text-[11px] font-bold uppercase tracking-tight whitespace-nowrap hover:bg-[#22A9C8] hover:text-white transition-all shadow-sm">
                                            <span class="hidden md:inline">Ver Historial y Reporte</span>
                                            <span class="md:hidden">Historial</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-8 border-t border-gray-100">
                    {{ $professionals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
