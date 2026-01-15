<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0D1E4C]">
                    Comunicación
                </h2>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Comunicación con profesionales -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-[#22A9C8] p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900">
                            Comunicación con profesionales
                        </h3>
                        <p class="text-gray-500 text-xs md:text-sm">
                            Envía un correo electrónico a todo tu equipo o a un profesional seleccionado.
                        </p>
                    </div>
                </div>

                <form action="{{ route('empleador.mass-email') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Destinatario
                            </label>
                            <select name="recipient_id"
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20">
                                <option value="">Todo el equipo de profesionales</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }} ({{ $employee->job_title ?? 'Profesional' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Asunto del mensaje
                            </label>
                            <input type="text"
                                   name="subject"
                                   required
                                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Cuerpo del mensaje
                        </label>
                        <textarea name="message"
                                  rows="4"
                                  required
                                  class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-[#22A9C8] hover:bg-[#1C8CA8] text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md flex items-center gap-2">
                            <span>Enviar mensaje</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
