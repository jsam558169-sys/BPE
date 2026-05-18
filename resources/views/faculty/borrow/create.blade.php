<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center font-header">
            <div>
                <h2 class="font-bold text-2xl text-brand-navy leading-tight tracking-tight">New Issuance Request</h2>
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

    <div id="equipment-data"
        data-equipment="{{ json_encode($equipments->map(fn($e) => ['id' => $e->equipment_id, 'name' => $e->equipment_name, 'stock' => $e->available_quantity])) }}"
        data-preselect="{{ request('equipment_id', '') }}"
        class="hidden">
    </div>

    <div id="main-wrapper" class="py-12 bg-app-bg min-h-screen font-body">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 font-semibold rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 p-6 border-b border-gray-200">
                    <h3 class="text-xs font-black text-brand-navy uppercase tracking-[0.2em]">Transaction Details</h3>
                </div>

                <form id="issuanceForm" action="{{ route('faculty.borrow.store') }}" method="POST" class="p-8"
                    onsubmit="return handleFormSubmit(this)">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 pb-8 border-b border-gray-100">
                        <div>
                            <label class="block text-[10px] font-black text-neutral-body uppercase tracking-widest mb-2">Expected Return Date*</label>
                            <input type="date" name="expected_return_date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('expected_return_date') }}"
                                class="w-full border-gray-300 rounded py-2 px-3 text-neutral-dark font-bold text-sm focus:ring-brand-orange focus:border-brand-orange">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-neutral-body uppercase tracking-widest mb-2">Expected Return Time*</label>
                            <input type="time" name="expected_return_time" required
                                value="{{ old('expected_return_time') }}"
                                class="w-full border-gray-300 rounded py-2 px-3 text-neutral-dark font-bold text-sm focus:ring-brand-orange focus:border-brand-orange">
                        </div>
                        <div class="flex items-center">
                            <p class="text-[11px] text-neutral-body italic leading-relaxed">
                                Set the date and time you plan to return the equipment. Same-day returns are allowed.
                            </p>
                        </div>
                    </div>

                    <h3 class="text-[10px] font-black text-neutral-body uppercase tracking-[0.2em] mb-4">Selected Equipment</h3>

                    <div id="duplicate-warning"
                        class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded uppercase tracking-wide">
                        ⚠ You selected the same equipment in multiple rows. Please combine or remove duplicates.
                    </div>

                    <div id="max-rows-warning"
                        class="hidden mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded uppercase tracking-wide">
                        ⚠ Maximum of 5 equipment types per request. Remove a row to add a different one.
                    </div>

                    <div id="equipment-rows" class="space-y-4"></div>

                    {{-- Add row button — fixed icon alignment --}}
                    <div class="mt-6">
                        <button type="button" onclick="addRow()"
                            class="group inline-flex items-center gap-2 text-brand-navy hover:text-brand-orange font-black text-[10px] uppercase tracking-widest transition-colors">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-navy group-hover:bg-brand-orange text-white transition-colors shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
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

    {{-- Delete confirmation modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-brand-navy/80 backdrop-blur-sm" onclick="cancelDelete()"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 border-t-4 border-red-500 p-6 z-10">
            <h3 class="text-sm font-black text-brand-navy uppercase tracking-widest mb-2">Remove Equipment Row?</h3>
            <p class="text-xs text-gray-500 mb-6">This row will be removed from your request. You can add it back if needed.</p>
            <div class="flex gap-3 justify-end">
                <button onclick="cancelDelete()"
                    class="px-5 py-2 text-[10px] font-black uppercase tracking-widest border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button id="confirm-delete-btn"
                    class="px-5 py-2 text-[10px] font-black uppercase tracking-widest bg-red-600 hover:bg-red-700 text-white rounded transition">
                    Remove
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Combobox styles */
        .combo-wrapper {
            position: relative;
        }

        .combo-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.4rem 2rem 0.4rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            background: white;
            cursor: pointer;
        }

        .combo-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 1px #f97316;
        }

        .combo-arrow {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #9ca3af;
        }

        .combo-dropdown {
            position: absolute;
            z-index: 50;
            width: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-height: 220px;
            overflow: hidden;
            display: none;
            flex-direction: column;
            top: calc(100% + 4px);
        }

        .combo-dropdown.open {
            display: flex;
        }

        .combo-search {
            padding: 0.4rem 0.6rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.7rem;
            outline: none;
            width: 100%;
        }

        .combo-list {
            overflow-y: auto;
            max-height: 170px;
        }

        .combo-option {
            padding: 0.45rem 0.75rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            color: #374151;
        }

        .combo-option:hover,
        .combo-option.highlighted {
            background: #fef3e2;
            color: #ea580c;
        }

        .combo-option.selected {
            background: #fff7ed;
            color: #c2410c;
        }

        .combo-option.disabled {
            color: #9ca3af;
            cursor: default;
            font-style: italic;
        }

        .combo-empty {
            padding: 0.6rem 0.75rem;
            font-size: 0.7rem;
            color: #9ca3af;
            font-style: italic;
        }
    </style>

    <script>
        const dataEl = document.getElementById('equipment-data');
        const equipments = JSON.parse(dataEl.dataset.equipment);
        const preselect = dataEl.dataset.preselect;

        const stockMap = {};
        equipments.forEach(e => {
            stockMap[e.id] = e.stock;
        });

        let rowCount = 0;
        let rowToDelete = null;

        // ── Combobox builder ───────────────────────────────────────────
        function buildCombo(idx, selectedId) {
            const selected = selectedId ? equipments.find(e => String(e.id) === String(selectedId)) : null;
            const displayVal = selected ? `${selected.name} (${selected.stock} available)` : '';

            return `
                <div class="combo-wrapper">
                    <input type="text"
                        class="combo-input"
                        placeholder="Search equipment..."
                        value="${displayVal}"
                        autocomplete="off"
                        readonly
                        onclick="openCombo(this)">
                    <span class="combo-arrow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                    <div class="combo-dropdown">
                        <input type="text" class="combo-search" placeholder="Type to search..." oninput="filterCombo(this)">
                        <div class="combo-list">
                            ${equipments.map(e => `
                                <div class="combo-option${String(e.id) === String(selectedId) ? ' selected' : ''}"
                                    data-id="${e.id}"
                                    data-name="${e.name}"
                                    data-stock="${e.stock}"
                                    onclick="selectComboOption(this)">
                                    ${e.name} <span style="color:#9ca3af;font-weight:400">(${e.stock} available)</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <input type="hidden" name="items[${idx}][equipment_id]"
                        class="equipment-select"
                        value="${selectedId || ''}">
                </div>`;
        }

        function openCombo(inputEl) {
            // Close all other dropdowns first
            document.querySelectorAll('.combo-dropdown.open').forEach(d => {
                if (d !== inputEl.nextElementSibling?.nextElementSibling) {
                    d.classList.remove('open');
                }
            });
            const wrapper = inputEl.closest('.combo-wrapper');
            const dropdown = wrapper.querySelector('.combo-dropdown');
            const search = dropdown.querySelector('.combo-search');
            dropdown.classList.toggle('open');
            if (dropdown.classList.contains('open')) {
                search.value = '';
                filterCombo(search); // show all
                setTimeout(() => search.focus(), 50);
            }
        }

        function filterCombo(searchInput) {
            const term = searchInput.value.toLowerCase();
            const list = searchInput.closest('.combo-dropdown').querySelector('.combo-list');
            let anyVis = false;
            list.querySelectorAll('.combo-option').forEach(opt => {
                const name = opt.dataset.name.toLowerCase();
                const show = name.includes(term);
                opt.style.display = show ? '' : 'none';
                if (show) anyVis = true;
            });
            // Show/hide empty state
            let empty = list.querySelector('.combo-empty');
            if (!anyVis) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'combo-empty';
                    empty.textContent = 'No equipment found.';
                    list.appendChild(empty);
                }
                empty.style.display = '';
            } else if (empty) {
                empty.style.display = 'none';
            }
        }

        function selectComboOption(optionEl) {
            const wrapper = optionEl.closest('.combo-wrapper');
            const textInput = wrapper.querySelector('.combo-input');
            const hiddenInput = wrapper.querySelector('.equipment-select');
            const dropdown = wrapper.querySelector('.combo-dropdown');

            const id = optionEl.dataset.id;
            const name = optionEl.dataset.name;
            const stock = optionEl.dataset.stock;

            textInput.value = `${name} (${stock} available)`;
            hiddenInput.value = id;

            // Mark selected
            dropdown.querySelectorAll('.combo-option').forEach(o => o.classList.remove('selected'));
            optionEl.classList.add('selected');
            dropdown.classList.remove('open');

            // Trigger stock + duplicate checks
            const row = wrapper.closest('.row-container');
            const qtyInput = row.querySelector('.quantity-input');
            qtyInput.max = stockMap[id] || 0;
            qtyInput.value = '';
            validateStock(qtyInput);
            checkDuplicates();
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.combo-wrapper')) {
                document.querySelectorAll('.combo-dropdown.open').forEach(d => d.classList.remove('open'));
            }
        });

        // ── Row builder ────────────────────────────────────────────────
        function createRow(isFirst, selectedId) {
            const idx = rowCount++;
            const div = document.createElement('div');
            div.className = 'flex flex-wrap md:flex-nowrap gap-4 items-end bg-gray-50 p-4 rounded border border-gray-100 row-container';

            const deleteBtn = isFirst ?
                `<button type="button" disabled class="text-gray-300 cursor-not-allowed p-2" title="Cannot remove first row">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                   </button>` :
                `<button type="button" onclick="confirmDelete(this)"
                       class="text-gray-400 hover:text-red-600 p-2 transition-colors" title="Remove row">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                   </button>`;

            div.innerHTML = `
                <div class="flex-1">
                    <label class="block text-[9px] font-black text-brand-navy uppercase tracking-widest mb-2">Equipment Name</label>
                    ${buildCombo(idx, selectedId)}
                </div>
                <div class="w-full md:w-48 relative">
                    <label class="block text-[9px] font-black text-brand-navy uppercase tracking-widest mb-2">Quantity</label>
                    <input type="number" name="items[${idx}][quantity]" min="1" required
                        oninput="validateStock(this)"
                        class="quantity-input w-full border-gray-300 rounded text-xs font-bold transition-all duration-200 focus:ring-brand-orange focus:border-brand-orange"
                        placeholder="0">
                    <span class="stock-error hidden absolute -top-6 left-0 bg-red-600 text-white text-[10px] px-2 py-1 rounded font-black shadow-lg uppercase whitespace-nowrap z-10">
                        MAX: 0
                    </span>
                </div>
                <div class="pb-1">${deleteBtn}</div>`;
            return div;
        }

        const MAX_ROWS = 5;

        function addRow() {
            const rows = document.querySelectorAll('.row-container');
            if (rows.length >= MAX_ROWS) {
                document.getElementById('max-rows-warning').classList.remove('hidden');
                return;
            }
            document.getElementById('equipment-rows').appendChild(createRow(false, null));
            if (document.querySelectorAll('.row-container').length >= MAX_ROWS) {
                document.getElementById('max-rows-warning').classList.remove('hidden');
                document.querySelector('[onclick="addRow()"]').classList.add('hidden');
            }
        }

        // ── Delete confirmation ────────────────────────────────────────
        function confirmDelete(btn) {
            rowToDelete = btn.closest('.row-container');
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('confirm-delete-btn').onclick = function() {
                if (rowToDelete) {
                    rowToDelete.remove();
                    rowToDelete = null;
                    checkDuplicates();
                    refreshSubmitBtn();
                    if (document.querySelectorAll('.row-container').length < MAX_ROWS) {
                        document.getElementById('max-rows-warning').classList.add('hidden');
                        document.querySelector('[onclick="addRow()"]').classList.remove('hidden');
                    }
                }
                cancelDelete();
            };
        }

        function cancelDelete() {
            rowToDelete = null;
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ── Validation helpers ─────────────────────────────────────────
        function getSelectedIds() {
            return [...document.querySelectorAll('.equipment-select')]
                .map(s => s.value).filter(v => v !== '');
        }

        function getTotalQtyForId(equipmentId) {
            let total = 0;
            document.querySelectorAll('.row-container').forEach(row => {
                const sel = row.querySelector('.equipment-select');
                const qty = row.querySelector('.quantity-input');
                if (sel && qty && sel.value == equipmentId && qty.value) {
                    total += parseInt(qty.value) || 0;
                }
            });
            return total;
        }

        function refreshSubmitBtn() {
            const hasErrors = document.querySelectorAll('.stock-error:not(.hidden)').length > 0;
            const ids = getSelectedIds();
            const hasDupes = ids.length !== new Set(ids).size;
            const btn = document.getElementById('submitBtn');
            if (hasErrors || hasDupes) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'grayscale');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'grayscale');
            }
        }

        function checkDuplicates() {
            const ids = getSelectedIds();
            const hasDupes = ids.length !== new Set(ids).size;
            document.getElementById('duplicate-warning').classList.toggle('hidden', !hasDupes);
            refreshSubmitBtn();
        }

        function validateStock(input) {
            const row = input.closest('.row-container');
            const sel = row.querySelector('.equipment-select');
            const id = sel ? sel.value : null;
            const max = id ? (stockMap[id] || 0) : 0;
            const val = parseInt(input.value) || 0;
            const total = id ? getTotalQtyForId(id) : val;
            const errorLabel = row.querySelector('.stock-error');
            const isOver = val > 0 && (val > max || total > max);

            if (isOver) {
                input.classList.add('bg-red-50', 'text-red-700', 'border-red-600', 'ring-1', 'ring-red-100');
                input.classList.remove('bg-white', 'text-neutral-dark');
                errorLabel.classList.remove('hidden');
                errorLabel.innerText = 'MAX: ' + max;
            } else {
                input.classList.remove('bg-red-50', 'text-red-700', 'border-red-600', 'ring-1', 'ring-red-100');
                input.classList.add('bg-white', 'text-neutral-dark');
                errorLabel.classList.add('hidden');
            }
            refreshSubmitBtn();
        }

        function handleFormSubmit(form) {
            // Validate all combos have a selection
            let allSelected = true;
            document.querySelectorAll('.equipment-select').forEach(s => {
                if (!s.value) allSelected = false;
            });
            if (!allSelected) {
                alert('Please select an equipment for all rows.');
                return false;
            }
            const ids = getSelectedIds();
            if (ids.length !== new Set(ids).size) {
                alert('Please remove duplicate equipment selections before submitting.');
                return false;
            }
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').innerText = 'Processing Request...';
            document.getElementById('loader').classList.remove('hidden');
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('equipment-rows');
            const firstRow = createRow(true, preselect || null);
            container.appendChild(firstRow);
            if (preselect) {
                const qtyInput = firstRow.querySelector('.quantity-input');
                if (qtyInput) validateStock(qtyInput);
            }
        });
    </script>
</x-app-layout>