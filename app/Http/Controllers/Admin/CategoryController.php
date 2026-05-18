<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = EquipmentCategory::withCount('equipment')->orderBy('category_name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:150|unique:equipment_categories,category_name',
        ]);

        EquipmentCategory::create(['category_name' => $request->category_name]);

        return redirect()->route('admin.categories.index')->with('success', 'Category added.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:150|unique:equipment_categories,category_name,' . $id . ',category_id',
        ]);

        EquipmentCategory::findOrFail($id)->update(['category_name' => $request->category_name]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        $cat = EquipmentCategory::withCount('equipment')->findOrFail($id);

        if ($cat->equipment_count > 0) {
            return redirect()->back()->with('error', 'Cannot delete a category that has equipment assigned to it.');
        }

        $cat->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
