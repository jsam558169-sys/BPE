<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\BorrowRecord;
use App\Models\BorrowRecordEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('category');

        if ($request->filled('search')) {
            $query->where('equipment_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Switched to a standard switch statement for maximum PHP compatibility
        // And using DB::table('equipment_categories') to avoid needing a Category model file
        switch ($request->sort) {
            case 'name_desc':
                $query->orderBy('equipment_name', 'desc');
                break;
            case 'stock_high':
                $query->orderBy('available_quantity', 'desc');
                break;
            case 'stock_low':
                $query->orderBy('available_quantity', 'asc');
                break;
            case 'category_asc':
                $query->orderBy(DB::table('equipment_categories')->select('category_name')->whereColumn('equipment_categories.category_id', 'equipment.category_id'), 'asc');
                break;
            case 'category_desc':
                $query->orderBy(DB::table('equipment_categories')->select('category_name')->whereColumn('equipment_categories.category_id', 'equipment.category_id'), 'desc');
                break;
            default:
                $query->orderBy('equipment_name', 'asc');
                break;
        }

        $equipment = $query->get();

        // Fetch categories using the DB facade instead of an Eloquent model
        $categories = DB::table('equipment_categories')->orderBy('category_name', 'asc')->get();

        $myHistory = BorrowRecord::with(['items.equipment', 'status', 'returnRecord'])
            ->where('borrower_id', Auth::guard('borrower')->id())
            ->orderBy('borrow_date', 'desc')
            ->get();

        return view('faculty.borrow', compact('equipment', 'myHistory', 'categories'));
    }

    public function history(Request $request)
    {
        $query = BorrowRecord::with(['items.equipment', 'status', 'returnRecord'])
            ->where('borrower_id', Auth::guard('borrower')->id());

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('borrow_record_id', 'like', "%{$term}%")
                    ->orWhereHas('items.equipment', function ($e) use ($term) {
                        $e->where('equipment_name', 'like', "%{$term}%");
                    });
            });
        }

        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('borrow_date', 'asc');
                break;
            case 'status':
                $query->orderBy('status_id', 'asc');
                break;
            default:
                $query->orderBy('borrow_date', 'desc');
                break;
        }

        $myHistory = $query->get();

        return view('faculty.history', compact('myHistory'));
    }

    public function create()
    {
        $equipments = Equipment::with('category')
            ->where('available_quantity', '>', 0)
            ->orderBy('equipment_name')
            ->get();

        return view('faculty.borrow.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expected_return_date' => 'required|date|after_or_equal:today',
            'expected_return_time' => 'required|date_format:H:i',
            'items'                => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:equipment,equipment_id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        $ids = array_column($request->items, 'equipment_id');

        if (count($ids) !== count(array_unique($ids))) {
            return back()->withInput()->with('error', 'You selected the same equipment more than once. Please combine them into one row.');
        }

        try {
            DB::transaction(function () use ($request) {
                $adminId = \App\Models\Admin::first()->admin_id;

                $borrow = BorrowRecord::create([
                    'borrower_id'          => Auth::guard('borrower')->id(),
                    'admin_id'             => $adminId,
                    'status_id'            => \App\Models\Status::where('status_name', 'Pending')->value('status_id'),
                    'borrow_date'          => now()->toDateString(),
                    'check_out_time'       => now()->format('H:i:s'),
                    'expected_return_date' => $request->expected_return_date,
                    'expected_return_time' => $request->expected_return_time . ':00',
                ]);

                foreach ($request->items as $itemData) {
                    $equipment = Equipment::lockForUpdate()->findOrFail($itemData['equipment_id']);

                    if ($equipment->available_quantity < $itemData['quantity']) {
                        throw new \Exception("Insufficient stock for {$equipment->equipment_name}. Only {$equipment->available_quantity} available.");
                    }

                    BorrowRecordEquipment::create([
                        'borrow_record_id'  => $borrow->borrow_record_id,
                        'equipment_id'      => $equipment->equipment_id,
                        'quantity_borrowed' => $itemData['quantity'],
                    ]);
                }
            });

            return redirect()->route('faculty.history')->with('success', 'Borrow request submitted successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
