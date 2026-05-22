<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\VEquipmentInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        // --- Use v_equipment_inventory view for the listing ---
        // It already joins category and admin, and computes availability_status.
        $equipment  = VEquipmentInventory::filtered(
            $request->search,
            $request->category,
            $request->sort
        );

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
            'equipment_name'     => $request->equipment_name,
            'category_id'        => $request->category_id,
            'admin_id'           => Auth::guard('admin')->id(),
            'total_quantity'     => $request->total_quantity,
            'available_quantity' => $request->total_quantity,
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment added successfully!');
    }

    public function edit($id)
    {
        // Edit still uses the real Equipment model — views are read-only.
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

        $item->fill([
            'equipment_name' => $request->equipment_name,
            'category_id'    => $request->category_id,
            'total_quantity' => $request->total_quantity,
        ]);

        if (!$item->isDirty()) {
            return redirect()->route('admin.equipment.index')->with('success', 'No changes made!');
        }

        $difference = $request->total_quantity - $item->getOriginal('total_quantity');
        $item->available_quantity = max(0, $item->available_quantity + $difference);
        $item->save();

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated!');
    }

    public function destroy($id)
    {
        $item = Equipment::findOrFail($id);

        if ($item->available_quantity < $item->total_quantity) {
            return redirect()->back()->with('error', 'Cannot delete equipment that is currently borrowed.');
        }

        $item->delete();
        return redirect()->route('admin.equipment.index')->with('success', 'Equipment deleted.');
    }
}
