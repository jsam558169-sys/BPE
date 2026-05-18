<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    {{ __('Transaction History') }}
                </h2>
                <p class="text-sm text-neutral-body">Complete log of all returned equipment transactions.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body min-h-screen" x-data="{ openModal: false, activeRemarks: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 font-semibold rounded">
                {{ session('error') }}
            </div>
            @endif

            {{-- Search Bar --}}
            <div class="mb-6 flex flex-col md:flex-row gap-3 items-center">
                <form method="GET" action="{{ route('admin.history.index') }}" class="flex w-full gap-2 items-center">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search borrower or equipment..."
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
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc'      ? 'selected' : '' }}>Oldest First</option>
                        <option value="borrower_asc" {{ request('sort') == 'borrower_asc'  ? 'selected' : '' }}>Borrower (A-Z)</option>
                        <option value="borrower_desc" {{ request('sort') == 'borrower_desc' ? 'selected' : '' }}>Borrower (Z-A)</option>
                        <option value="category_asc" {{ request('sort') == 'category_asc'  ? 'selected' : '' }}>Category (A-Z)</option>
                        <option value="category_desc" {{ request('sort') == 'category_desc' ? 'selected' : '' }}>Category (Z-A)</option>
                    </select>

                    <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php $hasFilters = request()->filled('search') || request()->filled('sort') || request()->filled('category'); @endphp
                    <a @if($hasFilters) href="{{ route('admin.history.index') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all {{ $hasFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
                        Clear All
                    </a>
                </form>
            </div>

            {{-- History Table --}}
            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Borrower</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Equipment</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Timeline</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Condition</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-brand-navy uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($completedLogs as $log)
                        @php
                        $condition = $log->returnRecord->condition ?? 'complete';
                        $conditionConfig = [
                        'complete' => ['label' => 'Complete', 'classes' => 'bg-green-50 text-green-700 border-green-200'],
                        'incomplete' => ['label' => 'Incomplete', 'classes' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
                        'damaged' => ['label' => 'Damaged', 'classes' => 'bg-red-50 text-red-700 border-red-200'],
                        ];
                        $cfg = $conditionConfig[$condition];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- Borrower --}}
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <div class="text-sm font-bold text-neutral-dark uppercase tracking-tight">
                                    {{ $log->borrower->full_name ?? 'Borrower Deleted' }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase italic">Completed Log</div>
                            </td>

                            {{-- Equipment --}}
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-2">
                                    @foreach($log->items as $item)
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gray-100 p-1.5 rounded text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-neutral-dark uppercase tracking-tight">{{ $item->equipment->equipment_name }}</span>
                                            <span class="text-[10px] font-black text-neutral-body">QTY: {{ $item->quantity_borrowed }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-1">
                                    @foreach($log->items as $item)
                                    <span class="block px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase w-fit">
                                        {{ $item->equipment->category->category_name ?? '—' }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Timeline --}}
                            <td class="px-6 py-4 align-top space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 uppercase">Out</span>
                                    <span class="text-[10px] font-bold text-neutral-body uppercase">{{ \Carbon\Carbon::parse($log->borrow_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black bg-brand-navy/10 px-1.5 py-0.5 rounded text-brand-navy uppercase">In</span>
                                    <span class="text-[10px] font-bold text-brand-navy uppercase">
                                        {{ $log->returnRecord ? \Carbon\Carbon::parse($log->returnRecord->return_date)->format('M d, Y') : '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Condition --}}
                            <td class="px-6 py-4 align-top">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border {{ $cfg['classes'] }}">
                                    {{ $cfg['label'] }}
                                </span>
                            </td>

                            {{-- Actions (Styled like Faculty Management) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">

                                    {{-- View Remarks --}}
                                    <button type="button" title="View Remarks"
                                        @click="openModal = true; activeRemarks = '{{ addslashes($log->returnRecord->remarks ?? 'No remarks provided.') }}';"
                                        class="p-2 rounded-lg text-brand-navy bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    {{-- Delete History Log --}}
                                    <form action="{{ route('admin.history.destroy', $log->borrow_record_id) }}" method="POST"
                                        onsubmit="return confirm('Delete this history record permanently?')" class="flex items-center">
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
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-body font-medium italic">No historical transactions.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Remarks Modal --}}
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openModal" x-cloak
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-brand-navy/80 backdrop-blur-sm" aria-hidden="true"></div>
                <div x-show="openModal" x-cloak
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="relative bg-white rounded-lg shadow-xl max-w-lg w-full border-t-4 border-brand-orange p-6">
                    <h3 class="text-lg font-black text-brand-navy uppercase tracking-widest mb-4 border-b pb-2">Admin Remarks</h3>
                    <p class="text-sm text-neutral-body bg-gray-50 p-4 rounded-md border border-gray-100 italic" x-text="activeRemarks"></p>
                    <div class="mt-4 text-right">
                        <button @click="openModal = false"
                            class="bg-brand-navy text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-widest hover:bg-slate-800 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>