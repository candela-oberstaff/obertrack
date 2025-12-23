<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Superadmin - Obertrack') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Admin Navigation Hub -->
            @include('admin.partials.nav')
            
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
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('chat', $p['user']->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors title="Chat Interno">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </a>
                                            @if($p['user']->phone_number)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p['user']->phone_number) }}" target="_blank" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="WhatsApp">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="mailto:{{ $p['user']->email }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="Email">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        </div>
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
