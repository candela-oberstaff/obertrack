<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.companies') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $company->company_name ?? $company->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Company Stats & Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 col-span-2">
                    <div class="flex items-start gap-4">
                        <x-user-avatar :user="$company" size="16" class="w-20 h-20 text-3xl"/>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $company->company_name ?? $company->name }}</h3>
                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $company->email }}
                            </div>
                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $company->country ?? 'País no especificado' }}
                            </div>
                            <div class="pt-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-bold text-xs">
                                    Cliente desde: {{ $company->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-400 font-bold uppercase tracking-wider">Profesionales</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</div>
                        </div>
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-400 font-bold uppercase tracking-wider">Tareas Creadas</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $totalTasksCreated }}</div>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professionals List -->
            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Equipo de Profesionales</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                <th class="px-6 py-4">Profesional</th>
                                <th class="px-6 py-4">Relación</th>
                                <th class="px-6 py-4">Estado Actividad</th>
                                <th class="px-6 py-4">Último Registro</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($employeesWithStatus as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ $p['relation'] === 'disconnected' ? 'opacity-60 grayscale bg-gray-50/80 italic' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <x-user-avatar :user="$p['user']" size="8" />
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $p['user']->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $p['user']->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($p['relation'] === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                Vinculado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-500 border border-gray-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Desvinculado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($p['status'] === 'red')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-[10px] font-bold uppercase">Inactivo</span>
                                        @elseif($p['status'] === 'yellow')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-bold uppercase">Alerta</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase">Activo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $p['last_registration'] ? \Carbon\Carbon::parse($p['last_registration'])->format('d/m/Y') : 'Nunca' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.professionals.show-details', $p['user']->id) }}" class="text-[#22A9C8] font-bold text-xs uppercase hover:underline">Ver Detalles</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
