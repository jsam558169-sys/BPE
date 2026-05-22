<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    {{ __('Admin Borrowing Log') }}
                </h2>
                <p class="text-sm text-neutral-body">Manage and monitor all equipment transactions.</p>
            </div>
        </div>
    </x-slot>

    {{-- Added openReceiveModal, returnUrl, and borrowerName to Alpine state --}}
    <div class="py-12 bg-app-bg font-body min-h-screen" x-data="{ openModal: false, openReceiveModal: false, returnUrl: '', borrowerName: '' }">
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

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 font-semibold rounded">
                {{ session('error') }}
            </div>
            @endif

            {{-- ACTIVE BORROWING TAB --}}
            <div>
                <div class="mb-6 flex flex-col md:flex-row gap-3 items-center">
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
                        <select name="active_category" onchange="this.form.submit()"
                            class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('active_category') == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                            @endforeach
                        </select>

                        <select name="active_sort" onchange="this.form.submit()"
                            class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                            <option value="date_desc" {{ request('active_sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="date_asc" {{ request('active_sort') == 'date_asc'      ? 'selected' : '' }}>Oldest First</option>
                            <option value="borrower_asc" {{ request('active_sort') == 'borrower_asc'  ? 'selected' : '' }}>Borrower (A-Z)</option>
                            <option value="borrower_desc" {{ request('active_sort') == 'borrower_desc' ? 'selected' : '' }}>Borrower (Z-A)</option>
                            <option value="category_asc" {{ request('active_sort') == 'category_asc'  ? 'selected' : '' }}>Category (A-Z)</option>
                            <option value="category_desc" {{ request('active_sort') == 'category_desc' ? 'selected' : '' }}>Category (Z-A)</option>
                        </select>
                        <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                            Search
                        </button>
                        @php $hasActiveFilters = request()->filled('active_search') || request()->filled('active_sort') || request()->filled('active_category'); @endphp
                        <a @if($hasActiveFilters) href="{{ route('admin.dashboard') }}" @endif
                            class="text-sm font-medium whitespace-nowrap px-2 transition-all {{ $hasActiveFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
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
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Dates</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Status</th>
                                {{-- CHANGED: text-right to text-center --}}
                                <th class="px-6 py-4 text-center text-xs font-black text-brand-navy uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <div class="text-sm font-bold text-neutral-dark uppercase tracking-tight">
                                        {{ $log->borrower->full_name ?? 'Borrower Deleted' }}
                                    </div>
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
                                                <span class="text-[10px] font-black text-brand-orange">QTY: {{ $item->quantity_borrowed }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="space-y-1">
                                        @foreach($log->items as $item)
                                        <span class="block px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase w-fit">
                                            {{ $item->equipment->category->category_name ?? '—' }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap align-top text-[10px] font-bold text-neutral-body uppercase">
                                    <div>{{ \Carbon\Carbon::parse($log->borrow_date)->format('M d, Y') }}</div>
                                    <div class="text-gray-400">Due: {{ \Carbon\Carbon::parse($log->expected_return_date)->format('M d, Y') }}</div>
                                    @if($overdueIds)
                                    <span class="text-red-500 font-black">OVERDUE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                        {{ $log->status->status_name }}
                                    </span>
                                </td>

                                {{-- CHANGED: text-right to text-center --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center align-middle">
                                    <button type="button"
                                        @click="returnUrl = '{{ route('admin.return', $log->borrow_record_id) }}'; borrowerName = '{{ addslashes($log->borrower->full_name ?? 'Borrower') }}'; openReceiveModal = true"
                                        class="bg-brand-orange hover:bg-orange-600 text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider shadow-sm transition-colors">
                                        Receive
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-body font-medium italic">No active borrowings.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dynamic Receive/Return Form Modal --}}
        <div x-show="openReceiveModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openReceiveModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 bg-brand-navy/80 backdrop-blur-sm" aria-hidden="true" @click="openReceiveModal = false"></div>

                <div x-show="openReceiveModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="relative bg-white rounded-lg shadow-xl max-w-md w-full border-t-4 border-brand-orange p-6">

                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-black text-brand-navy uppercase tracking-widest">Receive Equipment</h3>
                        <p class="text-xs text-neutral-body mt-1">Processing return for: <span class="font-bold text-neutral-dark uppercase" x-text="borrowerName"></span></p>
                    </div>

                    <form :action="returnUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-brand-navy uppercase tracking-wider mb-1">Return Condition</label>
                            <select name="condition" required
                                class="block w-full text-sm border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange uppercase font-bold text-neutral-dark py-2">
                                <option value="complete">Complete</option>
                                <option value="incomplete">Incomplete</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-brand-navy uppercase tracking-wider mb-1">Remarks</label>
                            <textarea name="remarks" rows="3" placeholder="REMARKS (optional)"
                                class="block w-full text-sm border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange uppercase placeholder-gray-400 font-medium"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t">
                            <button type="button" @click="openReceiveModal = false"
                                class="bg-gray-100 hover:bg-gray-200 text-neutral-dark px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition">
                                Cancel
                            </button>
                            <button type="submit" onclick="return confirm('Confirm receipt of equipment?')"
                                class="bg-brand-orange hover:bg-orange-600 text-white px-5 py-2 rounded text-xs font-bold uppercase tracking-wider transition shadow-sm">
                                Receive
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing Remarks Modal --}}
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 bg-brand-navy/80 backdrop-blur-sm" aria-hidden="true" @click="openModal = false"></div>

                <div x-show="openModal"
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