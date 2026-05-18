<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight">
                    {{ __('My Borrowing History') }}
                </h2>
                <p class="text-sm text-neutral-body">Review your previous and ongoing equipment rentals.</p>
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

            {{-- New Search and Sort Bar --}}
            <div class="mb-6 flex flex-col md:flex-row gap-3 items-center">
                <form method="GET" action="{{ route('faculty.history') }}" class="flex w-full gap-2 items-center">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search equipment or Ref #..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange sm:text-sm">
                    </div>

                    <select name="sort" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange sm:text-sm rounded-md">
                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Sort By Date...</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>By Status</option>
                    </select>

                    <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php
                    $hasFilters = request()->filled('search') || request()->filled('sort');
                    @endphp

                    <a @if($hasFilters) href="{{ route('faculty.history') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all 
                        {{ $hasFilters 
                            ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' 
                            : 'text-gray-300 cursor-not-allowed no-underline' }}">
                        Clear All
                    </a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Items Borrowed</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Timeline</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-black text-brand-navy uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($myHistory as $record)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    @foreach($record->items as $item)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-bold text-neutral-dark uppercase tracking-tight">
                                            {{ $item->equipment->equipment_name }}
                                        </span>
                                        <span class="px-2 py-0.5 bg-app-bg text-brand-navy text-[10px] font-black rounded border border-gray-200">
                                            x{{ $item->quantity_borrowed }}
                                        </span>
                                    </div>
                                    @endforeach
                                    <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">
                                        Ref: #{{ $record->borrow_record_id }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-red-700 uppercase tracking-tighter bg-red-50 px-1.5 py-0.5 rounded border border-red-100">Out:</span>
                                        <span class="text-xs font-bold text-brand-navy">
                                            {{ \Carbon\Carbon::parse($record->borrow_date)->format('M d, Y') }}
                                            <span class="text-neutral-body ml-1 font-normal italic">
                                                {{ \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-tighter bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">Due:</span>
                                        <span class="text-xs font-bold text-brand-navy">
                                            {{ \Carbon\Carbon::parse($record->expected_return_date)->format('M d, Y') }}
                                            <span class="text-neutral-body ml-1 font-normal italic">
                                                {{ \Carbon\Carbon::parse($record->expected_return_time)->format('h:i A') }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                $statusName = $record->status->status_name;
                                $badgeClass = match($statusName) {
                                'Returned' => 'bg-green-50 text-green-700 border-green-200',
                                'Cancelled' => 'bg-gray-100 text-gray-500 border-gray-200',
                                'Approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                default => 'bg-orange-50 text-orange-700 border-orange-200', // Pending
                                };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                    {{ $statusName }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-neutral-body font-medium italic">
                                No activity found matching your search.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>