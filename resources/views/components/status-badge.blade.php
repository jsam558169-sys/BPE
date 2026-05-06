@props(['status'])

@php
$classes = match($status) {
'Available' => 'bg-[#D1FAE5] text-[#198754]',
'Borrowed' => 'bg-[#FEF3C7] text-[#E8761A]',
'Overdue' => 'bg-[#FEE2E2] text-[#DC2626]',
'Reserved' => 'bg-[#DBEAFE] text-[#3A9BC4]',
default => 'bg-gray-100 text-gray-600',
};
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-widest $classes"]) }}>
    {{ $slot }}
</span>