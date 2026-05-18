<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">Add New Sports Equipment</h2>
        <p class="text-sm text-neutral-body">Fill in the details below to add a new item to the inventory.</p>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        /* Small style tweak to match your Tailwind border colors */
        .ts-control {
            border-color: #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        .ts-control.focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 1px #f97316 !important;
        }
    </style>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form action="{{ route('admin.equipment.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Equipment Name</label>
                        <input type="text" name="equipment_name" value="{{ old('equipment_name') }}"
                            placeholder="E.G. MOLTEN BASKETBALL GG7X" required
                            class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 uppercase text-sm placeholder:text-gray-300">
                        <x-input-error :messages="$errors->get('equipment_name')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Category</label>
                        <div class="flex gap-2 items-start">
                            <div class="flex-1 w-full min-w-0">
                                <select name="category_id" id="category-combobox" required
                                    class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 text-sm">
                                    <option value="">— Select a Category —</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="{{ route('admin.categories.index') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 h-[42px] border border-gray-200 rounded-lg text-[10px] font-black text-gray-500 uppercase tracking-widest hover:text-brand-navy hover:border-brand-navy transition shrink-0">
                                Manage
                            </a>
                        </div>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="mb-8">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Quantity</label>
                        <input type="number" name="total_quantity" value="{{ old('total_quantity') }}" min="1" placeholder="0" required
                            class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 text-sm">
                        <x-input-error :messages="$errors->get('total_quantity')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                        <button type="submit" class="bg-brand-orange text-white px-8 py-3 rounded shadow-md text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition">
                            Save Equipment
                        </button>
                        <a href="{{ route('admin.equipment.index') }}" class="text-[10px] font-black text-gray-400 uppercase hover:text-black tracking-widest transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#category-combobox', {
                create: false, // Set to true if you want users to be able to type new categories that don't exist yet
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "— Type to search or select a Category —"
            });
        });
    </script>
</x-app-layout>