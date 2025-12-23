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
                <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Listado de Profesionales</h3>
                        @if(isset($selectedCompany))
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-3 py-1 bg-[#22A9C8]/10 text-[#22A9C8] text-xs font-bold rounded-full">Filtrado por: {{ $selectedCompany->company_name ?? $selectedCompany->name }}</span>
                                <a href="{{ route('admin.professionals') }}" class="text-[10px] text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">Quitar filtro</a>
                            </div>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-gray-400">{{ $professionals->total() }} registros encontrados</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                <th class="px-8 py-4">Profesional</th>
                                <th class="px-8 py-4">Relación Actual</th>
                                <th class="px-8 py-4">Estado / Actividad</th>
                                <th class="px-8 py-4">Asignar a Empresa</th>
                                <th class="px-8 py-4 text-right">Reportes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($professionals as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <x-user-avatar :user="$p['user']" size="10" />
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $p['user']->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $p['user']->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($p['user']->empleador)
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-700">{{ $p['user']->empleador->company_name ?? $p['user']->empleador->name }}</span>
                                                <form action="{{ route('admin.unlink-professional', $p['user']->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desvincular este profesional?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[10px] text-red-400 hover:text-red-500 font-bold uppercase tracking-wider mt-1">Desvincular</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-gray-300 uppercase italic">Sin empresa</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col gap-1">
                                            @if($p['status'] === 'red')
                                                <span class="w-fit px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold text-[10px] uppercase">Inactivo (2+ días)</span>
                                            @elseif($p['status'] === 'yellow')
                                                <span class="w-fit px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-bold text-[10px] uppercase">Alerta (1 día)</span>
                                            @else
                                                <span class="w-fit px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-bold text-[10px] uppercase">Activo</span>
                                            @endif
                                            <span class="text-[10px] text-gray-400 font-medium italic">
                                                Visto el: {{ $p['last_registration'] ? \Carbon\Carbon::parse($p['last_registration'])->format('d/m/Y') : 'Nunca' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
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
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('reportes.show', $p['user']->id) }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-[#22A9C8] text-[#22A9C8] rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#22A9C8] hover:text-white transition-all">
                                            Ver Reporte
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
