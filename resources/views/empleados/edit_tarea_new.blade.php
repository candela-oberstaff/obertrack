@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Asignaciones de <span class="text-blue-600">{{ $empleador ? $empleador->name : 'No asignado' }}</span>
        </h1>

        @if(session('success'))
            <div class="bg-green-500 text-white p-2 rounded-md mb-4 text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($tareasEmpleador as $tarea)
                    <x-tasks.task-card :task="$tarea" />
                @empty
                    <div class="p-4 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay tareas asignadas</h3>
                        <p class="mt-1 text-sm text-gray-500">Cuando se te asignen tareas, aparecerán aquí.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/task-management.js'])
@endsection
