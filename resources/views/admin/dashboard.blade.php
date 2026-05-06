<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    {{ __('Admin Master Borrowing Log') }}
                </h2>
                <p class="text-sm text-neutral-body">Monitor and manage all equipment transactions across the university.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body min-h-screen" x-data="{ openModal: false, activeRemarks: '', tab: 'pending' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Notifications --}}
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

            {{-- Tab Navigation --}}
            <div class="flex items-center gap-2 mb-6 bg-gray-100 p-1 rounded-lg w-fit border border-gray-200">
                <button
                    @click="tab = 'pending'"
                    :class="tab === 'pending' ? 'bg-white text-brand-navy shadow-sm' : 'text-gray-500 hover:text-brand-navy'"
                    class="px-6 py-2 rounded-md text-xs font-black uppercase tracking-widest transition-all">
                    Active Borrowing
                </button>
                <button
                    @click="tab = 'completed'"
                    :class="tab === 'completed' ? 'bg-white text-brand-navy shadow-sm' : 'text-gray-500 hover:text-brand-navy'"
                    class="px-6 py-2 rounded-md text-xs font-black uppercase tracking-widest transition-all">
                    Transaction History
                </button>
            </div>

            {{-- ACTIVE BORROWING TAB --}}
            <div x-show="tab === 'pending'" x-transition>
                {{-- Search and Sort Bar --}}
                <div class="mb-4 flex flex-col md:flex-row gap-3 items-center">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex w-full gap-2 items-center">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" name="active_search" value="{{ request('active_search') }}"
                                placeholder="Search borrower or equipment..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange">
                        </div>

                        <select name="active_sort" onchange="this.form.submit()"
                            class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                            <option value="date_desc" {{ request('active_sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="date_asc" {{ request('active_sort') == 'date_asc'  ? 'selected' : '' }}>Oldest First</option>
                            <option value="borrower_asc" {{ request('active_sort') == 'borrower_asc'  ? 'selected' : '' }}>Borrower (A-Z)</option>
                            <option value="borrower_desc" {{ request('active_sort') == 'borrower_desc' ? 'selected' : '' }}>Borrower (Z-A)</option>
                        </select>

                        <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                            Search
                        </button>

                        @php $hasActiveFilters = request()->filled('active_search') || request()->filled('active_sort'); @endphp
                        <a @if($hasActiveFilters) href="{{ route('admin.dashboard') }}" @endif
                            class="text-sm font-medium whitespace-nowrap px-2 transition-all
                {{ $hasActiveFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
                            Clear All
                        </a>
                    </form>
                </div>


                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Borrower</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Equipment</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Dates</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-brand-navy uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <div class="text-sm font-bold text-neutral-dark uppercase tracking-tight">{{ $log->user->name ?? 'User Deleted' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase italic">Faculty Member</div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="space-y-2">
                                        @foreach($log->items as $item)
                                        <div class="flex items-center gap-3">
                                            <div class="bg-brand-navy/5 p-1.5 rounded text-brand-navy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-neutral-dark uppercase tracking-tight">{{ $item->equipment->equipment_name }}</span>
                                                <span class="text-[10px] font-black text-brand-orange">QTY: {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap align-top text-[10px] font-bold text-neutral-body uppercase">
                                    {{ \Carbon\Carbon::parse($log->borrow_date)->format('M d, y | h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                        {{ $log->status->status_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right align-top">
                                    <form action="{{ route('admin.return', $log->id) }}" method="POST" class="flex flex-col xl:flex-row items-end xl:items-center justify-end gap-2">
                                        @csrf
                                        <select name="condition" required class="text-[10px] font-black uppercase border-gray-200 rounded py-1.5 focus:ring-brand-orange focus:border-brand-orange">
                                            <option value="Good">Good</option>
                                            <option value="Damaged">Damaged</option>
                                            <option value="Lost">Lost</option>
                                        </select>
                                        <input type="text" name="remarks" placeholder="REMARKS..." class="text-[10px] font-black uppercase border-gray-200 rounded py-1.5 w-24 focus:ring-brand-orange focus:border-brand-orange">
                                        <button type="submit" onclick="return confirm('Confirm receipt of equipment?')" class="bg-brand-orange hover:bg-orange-600 text-white px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest shadow-sm transition-colors">
                                            Receive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-neutral-body font-medium italic">No active borrowings.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Search and Sort Bar for History (only shows on completed tab) --}}
            <div x-show="tab === 'completed'" class="mb-4 flex flex-col md:flex-row gap-3 items-center">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex w-full gap-2 items-center">
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

                    <select name="sort" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc'  ? 'selected' : '' }}>Oldest First</option>
                        <option value="borrower_asc" {{ request('sort') == 'borrower_asc'  ? 'selected' : '' }}>Borrower (A-Z)</option>
                        <option value="borrower_desc" {{ request('sort') == 'borrower_desc' ? 'selected' : '' }}>Borrower (Z-A)</option>
                    </select>

                    <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php $hasFilters = request()->filled('search') || request()->filled('sort'); @endphp
                    <a @if($hasFilters) href="{{ route('admin.dashboard') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all
            {{ $hasFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
                        Clear All
                    </a>
                </form>
            </div>

            {{-- TRANSACTION HISTORY TAB --}}
            <div x-show="tab === 'completed'" x-transition x-cloak>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Borrower</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Equipment</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Timeline</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Return Condition</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-brand-navy uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($completedLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <div class="text-sm font-bold text-neutral-dark uppercase tracking-tight">{{ $log->user->name ?? 'User Deleted' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase italic">Completed Log</div>
                                </td>
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
                                                <span class="text-[10px] font-black text-neutral-body">QTY: {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 uppercase">Out</span>
                                        <span class="text-[10px] font-bold text-neutral-body uppercase">{{ \Carbon\Carbon::parse($log->borrow_date)->format('M d, y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black bg-brand-navy/10 px-1.5 py-0.5 rounded text-brand-navy uppercase">In</span>
                                        <span class="text-[10px] font-bold text-brand-navy uppercase">{{ \Carbon\Carbon::parse($log->actual_return_date)->format('M d, y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $log->condition == 'Good' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                        {{ $log->condition }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View Remarks --}}
                                        <button
                                            @click="openModal = true; activeRemarks = '{{ addslashes($log->remarks ?? 'No remarks provided.') }}';"
                                            class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-brand-navy px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Remarks
                                        </button>

                                        {{-- Delete (only enabled if user is soft-deleted) --}}
                                        @if($log->user === null || $log->user->trashed())
                                        <form action="{{ route('admin.history.destroy', $log->id) }}" method="POST" class="m-0 p-0"
                                            onsubmit="return confirm('Delete this history record permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center border border-red-200 text-red-600 hover:bg-red-50 px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                        @else
                                        <button disabled
                                            class="inline-flex items-center justify-center border border-gray-100 text-gray-300 px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest cursor-not-allowed">
                                            Delete
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-neutral-body font-medium italic">No historical transactions.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Alpine.js Modal for Details --}}
        <div x-show="openModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-brand-navy/80 backdrop-blur-sm"></div>
                </div>

                <div x-show="openModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-brand-orange">

                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-black text-brand-navy uppercase tracking-widest mb-4 border-b pb-2">
                                    Admin Remarks
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-neutral-body leading-relaxed bg-gray-50 p-4 rounded-md border border-gray-100 italic" x-text="activeRemarks"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="openModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-brand-navy text-base font-bold text-white hover:bg-slate-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-xs uppercase tracking-widest transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>