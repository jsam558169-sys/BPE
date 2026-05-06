<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black text-gray-800 uppercase tracking-widest">Edit Equipment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form action="{{ route('admin.equipment.update', $item->equipment_id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Equipment Name</label>
                        <input type="text" name="equipment_name" value="{{ $item->equipment_name }}"
                            class="w-full border-gray-200 rounded-lg focus:ring-[#800000] focus:border-[#800000] font-bold text-gray-800 uppercase text-sm">
                    </div>

                    <div class="mb-8">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Quantity</label>
                        <input type="number" name="total_quantity" value="{{ $item->total_quantity }}"
                            class="w-full border-gray-200 rounded-lg focus:ring-[#800000] focus:border-[#800000] font-bold text-gray-800 text-sm">
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-[#800000] text-white px-8 py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-black transition shadow-md">
                            Update Equipment
                        </button>
                        <a href="{{ route('admin.equipment.index') }}" class="text-[10px] font-black text-gray-400 uppercase hover:text-black tracking-widest">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>