<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plantillas de Email') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Admin Navigation Hub -->
            @include('admin.partials.nav')

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Plantillas de Email</h3>
                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-bold">Gestiona formatos para comunicaciones masivas</p>
                    </div>
                    <a href="{{ route('admin.email-templates.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-[#22A9C8] text-white text-[10px] md:text-xs font-black uppercase tracking-widest rounded-xl hover:opacity-90 transition-all shadow-md active:scale-95">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Nueva Plantilla
                    </a>
                </div>

                <div class="overflow-x-auto">
                    @if($templates->isEmpty())
                        <div class="text-center py-20">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-envelope text-gray-300 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-400 uppercase tracking-widest">No hay plantillas</h4>
                            <p class="text-sm text-gray-400 mt-2">Empieza creando una nueva plantilla para tus correos.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                    <th class="px-8 py-4">Información de Plantilla</th>
                                    <th class="px-8 py-4">Asunto del Email</th>
                                    <th class="px-8 py-4 hidden md:table-cell">Última Actualización</th>
                                    <th class="px-8 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($templates as $template)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="font-bold text-gray-900 text-base leading-tight">{{ $template->title }}</div>
                                            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">ID Interno</div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="text-sm font-medium text-gray-600">{{ $template->subject }}</div>
                                        </td>
                                        <td class="px-8 py-6 hidden md:table-cell">
                                            <div class="flex items-center gap-1.5 text-gray-400">
                                                <i class="bi bi-clock-history text-[10px]"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-tighter">
                                                    Actualizado: <span class="text-gray-500">{{ $template->updated_at->format('d/m/Y H:i') }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.mass-email.show', ['template_id' => $template->id]) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-emerald-200 transition-all"
                                                   title="Usar Plantilla">
                                                    <i class="bi bi-play-fill mr-1.5 text-base leading-none"></i>
                                                    Usar
                                                </a>
                                                <a href="{{ route('admin.email-templates.edit', $template->id) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-blue-200 transition-all"
                                                   title="Editar">
                                                    <i class="bi bi-pencil-square mr-1.5 text-base leading-none"></i>
                                                    Editar
                                                </a>
                                                <form action="{{ route('admin.email-templates.destroy', $template->id) }}" method="POST" class="inline-block" onsubmit="return confirmFormSubmit(event, '¿Estás seguro de querer eliminar esta plantilla?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-red-200 transition-all" title="Eliminar">
                                                        <i class="bi bi-trash-fill mr-1.5 text-base leading-none"></i>
                                                        Borrar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                @if($templates instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-8 border-t border-gray-100">
                    {{ $templates->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
