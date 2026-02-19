<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-xl text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Google Forms</h2>
                            <p class="text-gray-500">Gestiona tus formularios de Google vinculados</p>
                        </div>
                    </div>

                    @if($isConnected)
                        <form method="POST" action="{{ route('google-forms.disconnect') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-100 transition duration-150">
                                Desconectar cuenta
                            </button>
                        </form>
                    @else
                        <a href="{{ route('google-forms.connect') }}" class="inline-flex items-center px-4 py-2 bg-[#22A9C8] text-white text-sm font-semibold rounded-lg hover:bg-[#1B8BA6] transition duration-150">
                            Conectar Google Forms
                        </a>
                    @endif
                </div>

                @if($isConnected)
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Conectado como: <strong>{{ $email }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($forms as $form)
                            <div class="p-4 border border-gray-200 rounded-xl hover:shadow-md transition duration-150 group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 truncate pr-2" title="{{ $form['name'] }}">
                                            {{ $form['name'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">ID: {{ $form['id'] }}</p>
                                    </div>
                                    <a href="{{ $form['webViewLink'] }}" target="_blank" class="text-gray-400 group-hover:text-[#22A9C8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center">
                                <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-500 font-medium">No se encontraron formularios</h3>
                                <p class="text-sm text-gray-400">Crea formularios en tu cuenta de Google para verlos aquí.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="py-20 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="bg-purple-50 text-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Conecta tu cuenta</h3>
                            <p class="text-gray-500 mb-8">Debes vincular tu cuenta de Google para poder gestionar tus formularios directamente desde Obertrack.</p>
                            <a href="{{ route('google-forms.connect') }}" class="inline-flex items-center px-8 py-3 bg-[#22A9C8] text-white font-bold rounded-xl hover:bg-[#1B8BA6] transition duration-150 shadow-lg shadow-[#22A9C8]/20">
                                Comenzar ahora
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
