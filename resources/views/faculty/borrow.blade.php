<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight">
                    Borrow Equipment
                </h2>
                <p class="text-sm text-neutral-body">Browse and borrow available sports equipment.</p>
            </div>

            {{-- Uniform Button --}}
            <a href="{{ route('faculty.borrow.create') }}"
                class="bg-brand-orange hover:bg-orange-600 text-white px-6 py-3 rounded shadow-md transition-all flex items-center gap-2 text-sm font-black uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                BORROW EQUIPMENT
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Notification --}}
            @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 font-semibold rounded flex items-center gap-3">
                <span class="bg-green-500 text-white rounded-full p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                {{ session('success') }}
            </div>
            @endif

            {{-- Search and Sort Bar --}}
            <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                <form method="GET" action="{{ route('faculty.borrow.index') }}" class="flex w-full md:w-2/3 gap-2 items-center">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search equipment name..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange sm:text-sm">
                    </div>

                    <select name="sort" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange sm:text-sm rounded-md">
                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Sort By...</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Highest Stock</option>
                        <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Lowest Stock</option>
                    </select>

                    <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php
                    $hasFilters = request()->filled('search') || request()->filled('sort');
                    @endphp

                    <a @if($hasFilters) href="{{ route('faculty.borrow.index') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all 
                        {{ $hasFilters 
                            ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' 
                            : 'text-gray-300 cursor-not-allowed no-underline' }}">
                        Clear All
                    </a>
                </form>
            </div>

            {{-- Equipment Table --}}
            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Equipment</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Stock Level</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-black text-brand-navy uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($equipment as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-app-bg rounded flex items-center justify-center text-brand-navy">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-neutral-dark">{{ $item->equipment_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->available_quantity > 0)
                                <x-status-badge status="Available">Ready</x-status-badge>
                                @else
                                <x-status-badge status="Overdue">Out of Stock</x-status-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-body">
                                <span class="font-bold text-neutral-dark">{{ $item->available_quantity }}</span> units
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">

                                    <a href="{{ route('faculty.borrow.create') }}"
                                        class="bg-brand-navy hover:bg-slate-800 text-white px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest transition-colors">
                                        Borrow
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-neutral-body font-medium italic">
                                No equipment found matching your search.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>