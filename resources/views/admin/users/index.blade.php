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

            {{-- Search and Sort Bar --}}
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
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc'  ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="email_asc" {{ request('sort') == 'email_asc' ? 'selected' : '' }}>Email (A-Z)</option>
                        <option value="email_desc" {{ request('sort') == 'email_desc' ? 'selected' : '' }}>Email (Z-A)</option>
                        <option value="newest" {{ request('sort') == 'newest'    ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest'    ? 'selected' : '' }}>Oldest First</option>
                    </select>

                    <button type="submit"
                        class="bg-brand-navy hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider transition-colors">
                        Search
                    </button>

                    @php $hasFilters = request()->filled('search') || request()->filled('sort'); @endphp
                    <a @if($hasFilters) href="{{ route('admin.users.index') }}" @endif
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
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Faculty Details</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest">Email Address</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-center">Registration Date</th>
                                <th class="px-6 py-4 text-xs font-black text-brand-navy uppercase tracking-widest text-right">Actions</th>
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
                                            <div class="text-sm font-bold text-neutral-dark uppercase">
                                                {{ $faculty->name }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Status: Active</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-neutral-body">
                                    {{ $faculty->email }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-600 border border-slate-100 uppercase">
                                        {{ $faculty->created_at->format('M d, Y') }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        {{-- Update this link when you have your edit route ready --}}
                                        <a href="{{ route('admin.users.edit', $faculty->id) }}"
                                            class="inline-flex items-center justify-center bg-brand-navy border border-brand-navy hover:bg-slate-800 hover:border-slate-800 text-white px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition-colors min-w-[70px]">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $faculty->id) }}" method="POST" class="m-0 p-0 flex"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $faculty->name }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center border border-red-200 text-red-600 hover:bg-red-50 px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition-colors min-w-[70px]">
                                                Delete
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
    </div>
</x-app-layout>