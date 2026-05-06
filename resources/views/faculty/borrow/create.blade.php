<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight">
                    New Issuance Request
                </h2>
                <p class="text-sm text-neutral-body">BPE Sports Equipment Management • UM</p>
            </div>
            <a href="{{ route('faculty.borrow.index') }}"
                class="text-xs font-black text-brand-navy hover:text-brand-orange flex items-center gap-2 transition uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Inventory
            </a>
        </div>
    </x-slot>

    <div id="main-wrapper" class="py-12 bg-app-bg min-h-screen font-body">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div id="form-card" class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                <div class="bg-gray-50 p-6 border-b border-gray-200">
                    <h3 class="text-xs font-black text-brand-navy uppercase tracking-[0.2em]">Transaction Details</h3>
                </div>

                <form id="issuanceForm" action="{{ route('faculty.borrow.store') }}" method="POST" class="p-8" onsubmit="return handleFormSubmit(this)">
                    @csrf

                    {{-- Date and Time Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 pb-8 border-b border-gray-100">
                        <div>
                            <label class="block text-[10px] font-black text-neutral-body uppercase tracking-widest mb-2">Expected Return Date</label>
                            <input type="date" name="expected_return_date" required min="{{ date('Y-m-d') }}"
                                class="w-full border-gray-300 rounded py-2 px-3 text-neutral-dark font-bold text-sm focus:ring-brand-orange focus:border-brand-orange">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-neutral-body uppercase tracking-widest mb-2">Time of Return</label>
                            <input type="time" name="return_time" required
                                class="w-full border-gray-300 rounded py-2 px-3 text-neutral-dark font-bold text-sm focus:ring-brand-orange focus:border-brand-orange">
                        </div>

                        <div class="flex items-center">
                            <p class="text-[11px] text-neutral-body italic leading-relaxed">Specify both date and time to ensure accurate availability tracking.</p>
                        </div>
                    </div>

                    <h3 class="text-[10px] font-black text-neutral-body uppercase tracking-[0.2em] mb-4">Selected Equipment</h3>

                    <div id="equipment-rows" class="space-y-4">
                        <div class="flex flex-wrap md:flex-nowrap gap-4 items-end bg-gray-50 p-4 rounded border border-gray-100 row-container">
                            <div class="flex-1">
                                <label class="block text-[9px] font-black text-gray-500 uppercase mb-2 text-brand-navy">Item Name</label>
                                <select name="items[0][equipment_id]" required onchange="updateMax(this)"
                                    class="equipment-select w-full border-gray-300 rounded text-xs font-bold uppercase tracking-tight focus:ring-brand-orange focus:border-brand-orange">
                                    <option value="" disabled selected>Select Item...</option>
                                    @foreach($equipments as $item)
                                    <option value="{{ $item->equipment_id }}" data-stock="{{ $item->available_quantity }}">
                                        {{ $item->equipment_name }} ({{ $item->available_quantity }} units left)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full md:w-48 relative">
                                <label class="block text-[9px] font-black text-gray-500 uppercase mb-2 text-brand-navy">Qty</label>
                                <input type="number" name="items[0][quantity]" min="1" required oninput="validateStock(this)"
                                    class="quantity-input w-full border-gray-300 rounded text-xs font-bold transition-all duration-200 focus:ring-brand-orange focus:border-brand-orange" placeholder="0">

                                <span class="stock-error hidden absolute -top-4 -right-2 bg-red-600 text-white text-[10px] px-3 py-1 rounded font-black shadow-lg animate-bounce uppercase border-2 border-white z-10">
                                    LIMIT: 0
                                </span>
                            </div>
                            <div class="pb-1">
                                <button type="button" class="text-gray-300 cursor-not-allowed p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="button" onclick="addRow()" class="group inline-flex items-center gap-2 text-brand-navy hover:text-brand-orange font-black text-[10px] uppercase tracking-widest transition-colors">
                            <span class="bg-brand-navy group-hover:bg-brand-orange text-white rounded-full w-5 h-5 flex items-center justify-center transition-colors">+</span>
                            Add Another Equipment
                        </button>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-100">
                        <button type="submit" id="submitBtn"
                            class="w-full bg-brand-orange hover:bg-orange-600 text-white font-bold py-4 px-12 rounded shadow-md uppercase tracking-[0.2em] text-xs transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <span id="btnText">Confirm Transaction & Process Borrowing</span>
                            <div id="loader" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowCount = 1;

        function updateMax(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const qtyInput = selectElement.closest('.row-container').querySelector('.quantity-input');
            qtyInput.max = stock;
            validateStock(qtyInput);
        }

        function validateStock(input) {
            const max = parseInt(input.max);
            const val = parseInt(input.value);
            const errorLabel = input.closest('div').querySelector('.stock-error');
            const submitBtn = document.getElementById('submitBtn');

            if (val > max) {
                input.classList.add('bg-red-50', 'text-red-700', 'border-red-600', 'ring-1', 'ring-red-100');
                input.classList.remove('bg-white', 'text-neutral-dark');
                errorLabel.classList.remove('hidden');
                errorLabel.innerText = `LIMIT: ${max}`;
            } else {
                input.classList.remove('bg-red-50', 'text-red-700', 'border-red-600', 'ring-1', 'ring-red-100');
                input.classList.add('bg-white', 'text-neutral-dark');
                errorLabel.classList.add('hidden');
            }

            const allErrors = document.querySelectorAll('.stock-error:not(.hidden)');
            if (allErrors.length > 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'grayscale');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'grayscale');
            }
        }

        function addRow() {
            const container = document.getElementById('equipment-rows');
            const newRow = document.createElement('div');
            newRow.className = "flex flex-wrap md:flex-nowrap gap-4 items-end bg-gray-50 p-4 rounded border border-gray-100 mt-3 animate-fadeIn row-container";
            newRow.innerHTML = `
                <div class="flex-1">
                    <select name="items[${rowCount}][equipment_id]" required onchange="updateMax(this)" class="equipment-select w-full border-gray-300 rounded text-xs font-bold uppercase tracking-tight focus:ring-brand-orange focus:border-brand-orange">
                        <option value="" disabled selected>Select Item...</option>
                        @foreach($equipments as $item)
                            <option value="{{ $item->equipment_id }}" data-stock="{{ $item->available_quantity }}">{{ $item->equipment_name }} ({{ $item->available_quantity }} units left)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48 relative">
                    <input type="number" name="items[${rowCount}][quantity]" min="1" required oninput="validateStock(this)" 
                        class="quantity-input w-full border-gray-300 rounded text-xs font-bold transition-all duration-200 focus:ring-brand-orange focus:border-brand-orange" placeholder="0">
                    <span class="stock-error hidden absolute -top-4 -right-2 bg-red-600 text-white text-[10px] px-3 py-1 rounded font-black shadow-lg animate-bounce uppercase border-2 border-white z-10">
                        LIMIT: 0
                    </span>
                </div>
                <div class="pb-1">
                    <button type="button" onclick="this.closest('.row-container').remove();" class="text-gray-400 hover:text-red-600 p-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>`;
            container.appendChild(newRow);
            rowCount++;
        }

        function handleFormSubmit(form) {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('btnText');
            const loader = document.getElementById('loader');
            btn.disabled = true;
            text.innerText = 'Processing Request...';
            loader.classList.remove('hidden');
            return true;
        }
    </script>
</x-app-layout>