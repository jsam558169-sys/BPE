<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight uppercase">Add New Sports Equipment</h2>
        <p class="text-sm text-neutral-body">Fill in the details below to add a new item to the inventory.</p>
    </x-slot>

    <div class="py-12 bg-app-bg font-body">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form action="{{ route('admin.equipment.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Equipment Name</label>
                        <input type="text" name="equipment_name" placeholder="E.G. MOLTEN BASKETBALL GG7X" required
                            class="w-full border-gray-200 rounded-lg focus:ring-brand-orange focus:border-brand-orange font-bold text-gray-800 uppercase text-sm placeholder:text-gray-300">
                        <x-input-error :messages="$errors->get('equipment_name')" class="mt-2" />
                    </div>

                    <div class="mb-8">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Quantity</label>
                        <input type="number" name="total_quantity" min="1" placeholder="0" required
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
</x-app-layout>