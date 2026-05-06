<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    // Show the form to edit an existing item
    public function edit($id)
    {
        $item = Equipment::findOrFail($id);
        return view('admin.equipment.edit', compact('item'));
    }

    // Save the changes from the edit form
    public function update(Request $request, $id)
    {
        $request->validate([
            'equipment_name' => 'required',
            'total_quantity' => 'required|integer|min:0',
        ]);

        $item = Equipment::findOrFail($id);

        // Logic check: If they increase total quantity, we should increase available too
        $difference = $request->total_quantity - $item->total_quantity;

        $item->update([
            'equipment_name' => $request->equipment_name,
            'total_quantity' => $request->total_quantity,
            'available_quantity' => $item->available_quantity + $difference,
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated!');
    }

    // Remove the item from the system
    public function destroy($id)
    {
        $item = Equipment::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment deleted!');
    }

    public function index(Request $request)
    {
        $query = Equipment::query();

        if ($request->filled('search')) {
            $query->where('equipment_name', 'like', '%' . $request->search . '%');
        }

        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('equipment_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('equipment_name', 'desc');
                break;
            case 'stock_high':
                $query->orderBy('available_quantity', 'desc');
                break;
            case 'stock_low':
                $query->orderBy('available_quantity', 'asc');
                break;
            default:
                $query->orderBy('equipment_name', 'asc');
                break;
        }

        $equipment = $query->get();

        return view('admin.equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_name' => 'required',
            'total_quantity' => 'required|integer|min:1',
        ]);

        Equipment::create([
            'equipment_name' => $request->equipment_name,
            'total_quantity' => $request->total_quantity,
            'available_quantity' => $request->total_quantity, // Initially, all are available
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'New Equipment added!');
    }
}
