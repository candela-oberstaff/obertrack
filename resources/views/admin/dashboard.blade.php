<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard del Analista - Obertrack') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Overall Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Total Profesionales</div>
                    <div class="text-2xl font-bold">{{ $stats['total_professionals'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500">Total Empresas</div>
                    <div class="text-2xl font-bold">{{ $stats['total_companies'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500">Alertas Amarillas</div>
                    <div class="text-2xl font-bold">{{ $stats['yellow_alerts'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500">Alertas Rojas</div>
                    <div class="text-2xl font-bold">{{ $stats['red_alerts'] }}</div>
                </div>
            </div>

            <!-- Professional Monitoring -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-bold border-b border-gray-100">
                    Monitoreo de Profesionales
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-xs font-bold text-gray-500">
                                <th class="px-4 py-3">Nombre</th>
                                <th class="px-4 py-3">Última Actividad</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($professionals as $p)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $p['user']->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $p['user']->email }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ $p['last_registration'] ? \Carbon\Carbon::parse($p['last_registration'])->format('d/m/Y') : 'Nunca' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($p['status'] === 'red')
                                            <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-800 font-bold text-xs uppercase">ROJO (2+ días)</span>
                                        @elseif($p['status'] === 'yellow')
                                            <span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold text-xs uppercase">AMARILLO (1 día)</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-bold text-xs uppercase">Activo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <button class="text-blue-600 hover:text-blue-900 font-medium">Contactar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mass Communication Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-bold border-b border-gray-100">
                    Comunicación Masiva
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.mass-email') }}" method="POST" class="space-y-4 max-w-2xl">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Segmento</label>
                            <select name="segment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="red_alerts">Alertas Rojas (Inactivos 2+ días)</option>
                                <option value="yellow_alerts">Alertas Amarillas (Inactivos 1 día)</option>
                                <option value="all_professionals">Todos los Profesionales</option>
                                <option value="all_companies">Todas las Empresas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asunto</label>
                            <input type="text" name="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mensaje</label>
                            <textarea name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            Enviar Emails
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
