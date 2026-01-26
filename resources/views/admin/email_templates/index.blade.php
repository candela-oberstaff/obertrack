<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Plantillas de Email') }}
                <span class="relative group inline-flex items-center ml-1 cursor-help">
    <span class=" rounded-full bg-blue-50 text-blue-500 hover:bg-blue-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
        </svg>
    </span>

    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                px-3 py-2 bg-gray-900 text-white text-[10px]
                rounded-lg opacity-0 group-hover:opacity-100
                transition-opacity duration-200
                whitespace-nowrap z-50 pointer-events-none shadow-xl">
         Gestiona formatos predefinidos para tus comunicaciones masivas
    </div>
</span>
            </h2>
            <a href="{{ route('admin.email-templates.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                Nueva Plantilla
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                 <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">&larr; Volver al Dashboard</a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($templates->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p class="mb-4">No hay plantillas creadas aún.</p>
                            <a href="{{ route('admin.email-templates.create') }}" class="text-blue-500 hover:underline">Crear la primera plantilla</a>
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto del Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actualización</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($templates as $template)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $template->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $template->subject }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $template->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.dashboard', ['template_id' => $template->id]) }}" class="text-green-600 hover:text-green-900 mr-4 font-bold">Usar</a>
                                            <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>
                                            <form action="{{ route('admin.email-templates.destroy', $template->id) }}" method="POST" class="inline-block" onsubmit="return confirmFormSubmit(event, '¿Estás seguro de querer eliminar esta plantilla?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
