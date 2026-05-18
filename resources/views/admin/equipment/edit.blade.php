<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">
                    Edit Equipment
                </h2>
                <p class="text-sm text-neutral-body">Update details for {{ $item->equipment_name }}</p>
            </div>
            <a href="{{ route('admin.equipment.index') }}"
                class="text-sm font-bold text-brand-navy hover:text-slate-700 transition-colors uppercase tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('admin.equipment.update', $item->equipment_id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-brand-navy uppercase tracking-widest mb-2">Equipment Name*</label>
                            <input type="text" name="equipment_name" value="{{ old('equipment_name', $item->equipment_name) }}"
                                class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-orange focus:ring-brand-orange text-sm" required>
                            @error('equipment_name') <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-brand-navy uppercase tracking-widest mb-2">Total Quantity*</label>
                            <input type="number" name="total_quantity" value="{{ old('total_quantity', $item->total_quantity) }}"
                                class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-orange focus:ring-brand-orange text-sm" required>
                            @error('total_quantity') <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-black text-brand-navy uppercase tracking-widest mb-2">Category*</label>
                        <select name="category_id"
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-brand-orange focus:ring-brand-orange text-sm text-gray-800" required>
                            <option value="">Select a Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id', $item->category_id) == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end items-center gap-6">
                        <a href="{{ route('admin.equipment.index') }}"
                            class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-brand-orange hover:bg-orange-600 text-white font-black px-8 py-3 rounded shadow-md transition-all text-xs tracking-widest uppercase">
                            Update Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>