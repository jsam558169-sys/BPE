<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    Equipment Inventory
                </h2>
                <p class="text-sm text-neutral-body">Manage and monitor your equipment.</p>
            </div>

            <a href="{{ route('admin.equipment.create') }}"
                class="bg-brand-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded shadow-md transition-all flex items-center gap-2 text-xs tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                ADD EQUIPMENT
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Notification --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 font-semibold rounded flex items-center gap-3">
                <span class="bg-green-500 text-white rounded-full p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                {{ session('success') }}
            </div>
            @endif

            {{-- Search and Sort Bar --}}
            <div class="mb-6 flex flex-col md:flex-row gap-3 items-center">
                <form method="GET" action="{{ route('admin.equipment.index') }}" class="flex w-full gap-2 items-center">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search equipment name..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange">
                    </div>

                    <select name="category" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->category_id }}" {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="sort" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc'  ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Highest Stock</option>
                        <option value="stock_low" {{ request('sort') == 'stock_low'  ? 'selected' : '' }}>Lowest Stock</option>
                        <option value="category_asc" {{ request('sort') == 'category_asc' ? 'selected' : '' }}>Category (A-Z)</option>
                        <option value="category_desc" {{ request('sort') == 'category_desc' ? 'selected' : '' }}>Category (Z-A)</option>
                    </select>

                    <button type="submit"
                        class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php $hasFilters = request()->filled('search') || request()->filled('sort') || request()->filled('category'); @endphp
                    <a @if($hasFilters) href="{{ route('admin.equipment.index') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all
            {{ $hasFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
                        Clear All
                    </a>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Equipment Details</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Category</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Total Stock</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Availability</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($equipment as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded flex items-center justify-center text-brand-navy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-neutral-dark uppercase">
                                                {{ $item->equipment_name }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">ID: #{{ str_pad($item->equipment_id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase">
                                        {{ $item->category->category_name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center font-bold text-neutral-body text-sm">
                                    {{ $item->total_quantity }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($item->available_quantity > 0)
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-green-50 text-green-700 border border-green-100 uppercase">
                                        {{ $item->available_quantity }} IN STOCK
                                    </span>
                                    @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-red-50 text-red-700 border border-red-100 uppercase">
                                        OUT OF STOCK
                                    </span>
                                    @endif
                                </td>

                                {{-- CENTERED icon actions matching Faculty Management style --}}
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-3">

                                        {{-- EDIT icon --}}
                                        <a href="{{ route('admin.equipment.edit', $item->equipment_id) }}" title="Edit"
                                            class="p-2 rounded-lg text-white bg-brand-navy hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- DELETE icon --}}
                                        <form action="{{ route('admin.equipment.destroy', $item->equipment_id) }}" method="POST"
                                            onsubmit="return confirm('Delete {{ addslashes($item->equipment_name) }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Delete"
                                                class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-neutral-body font-medium italic">
                                    No equipment currently in inventory.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>