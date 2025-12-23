<div class="flex gap-4 mb-8">
    <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 rounded-full text-sm font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#22A9C8] text-white shadow-lg shadow-[#22A9C8]/20' : 'bg-white text-gray-600 hover:bg-gray-100 shadow-sm' }}">
        Dashboard
    </a>
    <a href="{{ route('admin.companies') }}" class="px-6 py-2 rounded-full text-sm font-bold transition-all {{ request()->routeIs('admin.companies') ? 'bg-[#22A9C8] text-white shadow-lg shadow-[#22A9C8]/20' : 'bg-white text-gray-600 hover:bg-gray-100 shadow-sm' }}">
        Empresas
    </a>
    <a href="{{ route('admin.professionals') }}" class="px-6 py-2 rounded-full text-sm font-bold transition-all {{ request()->routeIs('admin.professionals') ? 'bg-[#22A9C8] text-white shadow-lg shadow-[#22A9C8]/20' : 'bg-white text-gray-600 hover:bg-gray-100 shadow-sm' }}">
        Profesionales
    </a>
</div>
