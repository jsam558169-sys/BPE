<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 text-pink-600">Available Sports Equipment</h3>
                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2 text-left">Equipment Name</th>
                            <th class="border px-4 py-2 text-left">Available</th>
                            <th class="border px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipment as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->equipment_name }}</td>
                            <td class="border px-4 py-2">{{ $item->available_quantity }} / {{ $item->total_quantity }}</td>
                            <td class="border px-4 py-2">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded">Borrow</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>