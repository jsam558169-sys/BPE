<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">Equipment Categories</h2>
                <p class="text-sm text-neutral-body">Manage categories for organising your inventory.</p>
            </div>
            <a href="{{ route('admin.equipment.create') }}"
                class="text-xs font-black text-brand-navy hover:text-brand-orange flex items-center gap-2 transition uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Add Equipment
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body min-h-screen" x-data="{ editId: null, editName: '' }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 font-semibold rounded">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 font-semibold rounded">
                {{ session('error') }}
            </div>
            @endif

            {{-- Add Category --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-[10px] font-black text-brand-navy uppercase tracking-[0.2em] mb-4">Add New Category</h3>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="category_name" value="{{ old('category_name') }}"
                        placeholder="E.G. BALL SPORTS"
                        required
                        class="flex-1 border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 uppercase text-sm placeholder:text-gray-300">
                    <button type="submit"
                        class="bg-brand-orange text-white px-6 py-2 rounded text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition">
                        Add
                    </button>
                </form>
                <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
            </div>

            {{-- Category List --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Category Name</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-brand-navy uppercase tracking-widest">Equipment</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-brand-navy uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                {{-- View mode --}}
                                <span x-show="editId !== {{ $cat->category_id }}" x-cloak
                                    class="text-sm font-bold text-neutral-dark uppercase">
                                    {{ $cat->category_name }}
                                </span>
                                {{-- Edit mode --}}
                                <form x-show="editId === {{ $cat->category_id }}" x-cloak
                                    action="{{ route('admin.categories.update', $cat->category_id) }}" method="POST"
                                    class="flex gap-2" id="edit-form-{{ $cat->category_id }}">
                                    @csrf @method('PUT')
                                    <input type="text" name="category_name" x-model="editName" required
                                        class="border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 uppercase text-sm w-full">
                                    <button type="submit"
                                        class="bg-brand-navy text-white px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition">
                                        Save
                                    </button>
                                    <button type="button" @click="editId = null"
                                        class="px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-neutral-body">
                                {{ $cat->equipment_count }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2" x-show="editId !== {{ $cat->category_id }}" x-cloak>
                                    <button @click="editId = {{ $cat->category_id }}; editName = '{{ addslashes($cat->category_name) }}'"
                                        class="inline-flex items-center border border-gray-200 text-brand-navy hover:bg-gray-50 px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $cat->category_id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center border border-red-200 text-red-600 hover:bg-red-50 px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-neutral-body font-medium italic">No categories yet. Add one above.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>