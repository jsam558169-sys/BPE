<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRecord;
use App\Models\ReturnRecord;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- ACTIVE BORROWS (no return record yet) ---
        $activeQuery = BorrowRecord::with(['borrower', 'items.equipment', 'status'])
            ->whereDoesntHave('returnRecord');

        if ($request->filled('active_search')) {
            $search = $request->active_search;
            $activeQuery->where(function ($q) use ($search) {
                $q->whereHas('borrower', fn($b) => $b->withTrashed()
                    ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%"))
                    ->orWhereHas('items.equipment', fn($e) => $e->where('equipment_name', 'like', "%{$search}%"));
            });
        }

        match ($request->active_sort) {
            'borrower_asc'   => $activeQuery->join('borrowers', 'borrow_records.borrower_id', '=', 'borrowers.borrower_id')
                ->orderBy('borrowers.last_name', 'asc')->select('borrow_records.*'),
            'borrower_desc'  => $activeQuery->join('borrowers', 'borrow_records.borrower_id', '=', 'borrowers.borrower_id')
                ->orderBy('borrowers.last_name', 'desc')->select('borrow_records.*'),
            'date_asc'       => $activeQuery->orderBy('borrow_date', 'asc'),
            'category_asc'   => $activeQuery->join('borrow_record_equipment', 'borrow_records.borrow_record_id', '=', 'borrow_record_equipment.borrow_record_id')
                ->join('equipment', 'borrow_record_equipment.equipment_id', '=', 'equipment.equipment_id')
                ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'asc')->select('borrow_records.*')->distinct(),
            'category_desc'  => $activeQuery->join('borrow_record_equipment', 'borrow_records.borrow_record_id', '=', 'borrow_record_equipment.borrow_record_id')
                ->join('equipment', 'borrow_record_equipment.equipment_id', '=', 'equipment.equipment_id')
                ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'desc')->select('borrow_records.*')->distinct(),
            default          => $activeQuery->orderBy('borrow_date', 'desc'),
        };

        if ($request->filled('active_category')) {
            $activeQuery->whereHas('items.equipment', fn($e) => $e->where('category_id', $request->active_category));
        }

        $pendingLogs = $activeQuery->get();
        $categories  = \App\Models\EquipmentCategory::orderBy('category_name')->get();

        return view('admin.dashboard', compact('pendingLogs', 'categories'));
    }

    public function history(Request $request)
    {
        $historyQuery = BorrowRecord::with(['borrower', 'items.equipment', 'status', 'returnRecord'])
            ->whereHas('returnRecord');

        if ($request->filled('search')) {
            $search = $request->search;
            $historyQuery->where(function ($q) use ($search) {
                $q->whereHas('borrower', fn($b) => $b->withTrashed()
                    ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%"))
                    ->orWhereHas('items.equipment', fn($e) => $e->where('equipment_name', 'like', "%{$search}%"));
            });
        }

        match ($request->sort) {
            'borrower_asc'   => $historyQuery->join('borrowers', 'borrow_records.borrower_id', '=', 'borrowers.borrower_id')
                ->orderBy('borrowers.last_name', 'asc')->select('borrow_records.*'),
            'borrower_desc'  => $historyQuery->join('borrowers', 'borrow_records.borrower_id', '=', 'borrowers.borrower_id')
                ->orderBy('borrowers.last_name', 'desc')->select('borrow_records.*'),
            'date_asc'       => $historyQuery->orderBy('borrow_date', 'asc'),
            'category_asc'   => $historyQuery->join('borrow_record_equipment', 'borrow_records.borrow_record_id', '=', 'borrow_record_equipment.borrow_record_id')
                ->join('equipment', 'borrow_record_equipment.equipment_id', '=', 'equipment.equipment_id')
                ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'asc')->select('borrow_records.*')->distinct(),
            'category_desc'  => $historyQuery->join('borrow_record_equipment', 'borrow_records.borrow_record_id', '=', 'borrow_record_equipment.borrow_record_id')
                ->join('equipment', 'borrow_record_equipment.equipment_id', '=', 'equipment.equipment_id')
                ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.category_id')
                ->orderBy('equipment_categories.category_name', 'desc')->select('borrow_records.*')->distinct(),
            default          => $historyQuery->orderBy('borrow_date', 'desc'),
        };

        if ($request->filled('category')) {
            $historyQuery->whereHas('items.equipment', fn($e) => $e->where('category_id', $request->category));
        }

        $completedLogs = $historyQuery->get();
        $categories    = \App\Models\EquipmentCategory::orderBy('category_name')->get();

        return view('admin.history', compact('completedLogs', 'categories'));
    }

    public function markAsReturned(Request $request, $id)
    {
        $request->validate([
            'remarks'   => 'nullable|string|max:1000',
            'condition' => 'required|in:complete,incomplete,damaged',
        ]);

        $record = BorrowRecord::with('items.equipment')->findOrFail($id);

        if ($record->returnRecord) {
            return redirect()->back()->with('error', 'This record is already marked as returned.');
        }

        DB::transaction(function () use ($record, $request) {
            ReturnRecord::create([
                'borrow_record_id' => $record->borrow_record_id,
                'admin_id'         => Auth::guard('admin')->id(),
                'return_date'      => now()->toDateString(),
                'return_time'      => now()->toTimeString(),
                'remarks'          => $request->remarks,
                'condition'        => $request->condition,
            ]);
        });

        return redirect()->back()->with('success', 'Equipment returned successfully! Stock updated.');
    }

    public function destroyHistory($id)
    {
        $record = BorrowRecord::with('returnRecord')->whereHas('returnRecord')->findOrFail($id);

        DB::transaction(function () use ($record) {
            $record->returnRecord->delete(); // delete child first
            $record->delete();              // then delete parent
        });

        return redirect()->back()->with('success', 'History record deleted.');
    }
}
