<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('category', 'admin');

        if ($request->filled('search')) {
            $query->where('equipment_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        match ($request->sort) {
            'name_desc'     => $query->orderBy('equipment_name', 'desc'),
            'stock_high'    => $query->orderBy('available_quantity', 'desc'),
            'stock_low'     => $query->orderBy('available_quantity', 'asc'),
            'category_asc'  => $query->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'asc')->select('equipment.*'),
            'category_desc' => $query->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'desc')->select('equipment.*'),
            default         => $query->orderBy('equipment_name', 'asc'),
        };

        $equipment  = $query->get();
        $categories = EquipmentCategory::orderBy('category_name')->get();

        return view('admin.equipment.index', compact('equipment', 'categories'));
    }

    public function create()
    {
        $categories = EquipmentCategory::orderBy('category_name')->get();
        return view('admin.equipment.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_name' => 'required|string|max:200',
            'category_id'    => 'required|exists:equipment_categories,category_id',
            'total_quantity' => 'required|integer|min:1',
        ]);

        Equipment::create([
            'equipment_name'    => $request->equipment_name,
            'category_id'       => $request->category_id,
            'admin_id'          => Auth::guard('admin')->id(),
            'total_quantity'    => $request->total_quantity,
            'available_quantity' => $request->total_quantity, // all available on creation
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment added successfully!');
    }

    public function edit($id)
    {
        $item       = Equipment::findOrFail($id);
        $categories = EquipmentCategory::orderBy('category_name')->get();
        return view('admin.equipment.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'equipment_name' => 'required|string|max:200',
            'category_id'    => 'required|exists:equipment_categories,category_id',
            'total_quantity' => 'required|integer|min:0',
        ]);

        $item = Equipment::findOrFail($id);

        // Prepare the updated data attributes
        $item->fill([
            'equipment_name' => $request->equipment_name,
            'category_id'    => $request->category_id,
            'total_quantity' => $request->total_quantity,
        ]);

        // Check if anything actually changed
        if (!$item->isDirty()) {
            // Changed from 'info' to 'success' to hook into your existing alert layout
            return redirect()->route('admin.equipment.index')->with('success', 'No changes made!');
        }

        // Since total_quantity might have changed, calculate the difference using the original database value
        $difference = $request->total_quantity - $item->getOriginal('total_quantity');
        $item->available_quantity = max(0, $item->available_quantity + $difference);

        $item->save();

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated!');
    }

    public function destroy($id)
    {
        $item = Equipment::findOrFail($id);

        // Guard: don't delete equipment that's currently borrowed
        if ($item->available_quantity < $item->total_quantity) {
            return redirect()->back()->with('error', 'Cannot delete equipment that is currently borrowed.');
        }

        $item->delete();
        return redirect()->route('admin.equipment.index')->with('success', 'Equipment deleted.');
    }
}
