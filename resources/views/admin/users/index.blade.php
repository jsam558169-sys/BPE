<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    {{ __('Faculty Management') }}
                </h2>
                <p class="text-sm text-neutral-body">Manage and monitor registered faculty accounts.</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="bg-brand-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded shadow-md transition-all flex items-center gap-2 text-xs tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                </svg>
                ADD NEW FACULTY
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body"
        x-data="{
            showModal: false,
            showResetForm: false,
            showNewPassword: false,
            active: { id: '', name: '', email: '', contact: '', middle: '', joined: '' },
            open(f) {
                this.active = f;
                this.showResetForm = false;
                this.showNewPassword = false;
                this.showModal = true;
            }
        }">
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
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 font-semibold rounded flex items-center gap-3">
                <span class="bg-red-500 text-white rounded-full p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span>
                {{ session('error') }}
            </div>
            @endif

            {{-- Search and Sort --}}
            <div class="mb-6 flex flex-col md:flex-row gap-3 items-center">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full gap-2 items-center">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or email..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-brand-orange focus:border-brand-orange">
                    </div>
                    <select name="sort" onchange="this.form.submit()"
                        class="block pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-brand-orange focus:border-brand-orange rounded-md">
                        <option value="name_asc" {{ request('sort','name_asc')=='name_asc'  ? 'selected':'' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected':'' }}>Name (Z-A)</option>
                        <option value="email_asc" {{ request('sort')=='email_asc' ? 'selected':'' }}>Email (A-Z)</option>
                        <option value="email_desc" {{ request('sort')=='email_desc'? 'selected':'' }}>Email (Z-A)</option>
                        <option value="newest" {{ request('sort')=='newest'    ? 'selected':'' }}>Newest First</option>
                        <option value="oldest" {{ request('sort')=='oldest'    ? 'selected':'' }}>Oldest First</option>
                    </select>
                    <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">Search</button>
                    @php $hasFilters = request()->filled('search') || request()->filled('sort'); @endphp
                    <a @if($hasFilters) href="{{ route('admin.users.index') }}" @endif
                        class="text-sm font-medium whitespace-nowrap px-2 transition-all {{ $hasFilters ? 'text-gray-500 hover:text-brand-orange underline cursor-pointer' : 'text-gray-300 cursor-not-allowed' }}">
                        Clear All
                    </a>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Faculty Details</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Email Address</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Registration Date</th>
                                {{-- CENTERED actions header --}}
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($facultyMembers as $faculty)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-neutral-dark uppercase">{{ $faculty->full_name }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Status: Active</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-neutral-body">{{ $faculty->email }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-600 border border-slate-100 uppercase">
                                        {{ $faculty->created_at->format('M d, Y') }}
                                    </span>
                                </td>

                                {{-- CENTERED icon actions --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-3">

                                        {{-- VIEW icon --}}
                                        <button type="button" title="View Details"
                                            @click="open({
                                                id: '{{ $faculty->borrower_id }}',
                                                name: '{{ addslashes($faculty->full_name) }}',
                                                email: '{{ addslashes($faculty->email) }}',
                                                contact: '{{ addslashes($faculty->contact_number ?? 'N/A') }}',
                                                middle: '{{ addslashes($faculty->middle_name ?? 'N/A') }}',
                                                joined: '{{ $faculty->created_at->format('M d, Y') }}'
                                            })"
                                            class="p-2 rounded-lg text-brand-navy bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        {{-- EDIT icon --}}
                                        <a href="{{ route('admin.users.edit', $faculty->borrower_id) }}" title="Edit"
                                            class="p-2 rounded-lg text-white bg-brand-navy hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- DELETE icon --}}
                                        <form action="{{ route('admin.users.destroy', $faculty->borrower_id) }}" method="POST"
                                            onsubmit="return confirm('Delete {{ addslashes($faculty->full_name) }}? This cannot be undone.')">
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
                                <td colspan="4" class="px-6 py-10 text-center text-neutral-body font-medium italic">
                                    No faculty members registered yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- VIEW MODAL --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-brand-navy/80 backdrop-blur-sm" @click="showModal = false"></div>
            <div x-show="showModal"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 border-t-4 border-brand-orange overflow-hidden">

                <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-black text-brand-navy uppercase tracking-widest">Faculty Details</h3>
                    <p class="text-xs text-gray-400 mt-1">Read-only view. Use Edit to update info.</p>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Full Name</p>
                            <p class="text-sm font-bold text-neutral-dark uppercase" x-text="active.name"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Middle Name</p>
                            <p class="text-sm font-bold text-neutral-dark" x-text="active.middle"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                            <p class="text-sm font-bold text-neutral-dark" x-text="active.email"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contact</p>
                            <p class="text-sm font-bold text-neutral-dark" x-text="active.contact"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Registered</p>
                            <p class="text-sm font-bold text-neutral-dark" x-text="active.joined"></p>
                        </div>
                    </div>

                    {{-- Password Reset Section --}}
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Password</p>
                                <p class="text-[9px] text-gray-400 italic mt-0.5">Passwords are hashed and cannot be viewed. You can reset it below.</p>
                            </div>
                            <button type="button" @click="showResetForm = !showResetForm"
                                class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded border border-brand-orange text-brand-orange hover:bg-orange-50 transition-colors whitespace-nowrap">
                                <span x-text="showResetForm ? 'Cancel' : 'Reset Password'"></span>
                            </button>
                        </div>

                        <div x-show="showResetForm" x-transition>
                            <form method="POST" :action="'/admin/users/' + active.id + '/reset-password'">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-3">
                                    <div class="relative">
                                        <input :type="showNewPassword ? 'text' : 'password'"
                                            name="password" required placeholder="New password"
                                            class="w-full border-gray-200 rounded-lg text-sm pr-10 focus:ring-brand-orange focus:border-brand-orange">
                                        <button type="button" @click="showNewPassword = !showNewPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-navy">
                                            <svg x-show="!showNewPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showNewPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="password" name="password_confirmation" required placeholder="Confirm new password"
                                        class="w-full border-gray-200 rounded-lg text-sm focus:ring-brand-orange focus:border-brand-orange">
                                    <button type="submit"
                                        class="w-full bg-brand-orange hover:bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded transition-colors">
                                        Set New Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button @click="showModal = false"
                        class="bg-brand-navy text-white px-6 py-2 rounded text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>