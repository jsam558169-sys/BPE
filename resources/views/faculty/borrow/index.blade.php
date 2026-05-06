<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Equipment Inventory</h2>
                    <p class="text-[#800000] text-[10px] font-bold uppercase tracking-[0.2em]">Available Equipment for Issuance • UM</p>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('faculty.history') }}" class="text-[10px] font-black text-gray-400 hover:text-black transition uppercase tracking-widest py-3">
                        View My History
                    </a>
                    <a href="{{ route('faculty.borrow.create') }}" class="bg-[#800000] hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg uppercase tracking-widest text-xs transition-all flex items-center gap-2">
                        <span class="text-lg">+</span> New Borrow Request
                    </a>
                </div>
            </div>

            {{-- Inventory Table --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Equipment Name</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Available Stock</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($equipments as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-tight group-hover:text-[#800000] transition-colors">
                                    {{ $item->equipment_name }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="font-mono font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-md text-xs">
                                    {{ $item->available_quantity }} units
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                @if($item->available_quantity > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">
                                    Available
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-400 border border-red-100">
                                    Out of Stock
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-20 text-center text-gray-400 italic uppercase text-[10px] tracking-widest">
                                No equipment found in the database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>