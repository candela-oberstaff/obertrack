<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Empresas - Dashboard Superadmin') }}
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
                    <h3 class="text-2xl font-bold text-gray-900">Listado de Empresas</h3>
                    <span class="text-sm font-medium text-gray-400">{{ $companies->total() }} registros encontrados</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                <th class="px-8 py-4">Empresa</th>
                                <th class="px-8 py-4">Contacto Relacionado</th>
                                <th class="px-8 py-4">Profesionales</th>
                                <th class="px-8 py-4">País</th>
                                <th class="px-8 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($companies as $company)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <x-user-avatar :user="$company" size="10" />
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $company->company_name ?? $company->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $company->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-gray-600 font-medium">
                                        {{ $company->related_contact ?? 'N/A' }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-bold text-xs">
                                            {{ $company->empleados_count }} profesionales
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-gray-500 font-medium italic">
                                        {{ $company->country ?? 'No especificado' }}
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('admin.professionals', ['company_id' => $company->id]) }}" class="text-[#22A9C8] hover:text-[#1a8ba6] font-bold text-xs uppercase tracking-wider">Detalles</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-8 border-t border-gray-100 italic font-medium">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
