<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- ACTIVE BORROWING (pending) ---
        $pendingQuery = BorrowRecord::with(['user', 'items.equipment', 'status'])
            ->whereNull('actual_return_date');

        if ($request->filled('active_search')) {
            $search = $request->active_search;
            $pendingQuery->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->withTrashed()->where('name', 'like', "%$search%"))
                    ->orWhereHas('items.equipment', fn($e) => $e->where('equipment_name', 'like', "%$search%"));
            });
        }

        switch ($request->active_sort) {
            case 'borrower_asc':
                $pendingQuery->join('users', 'borrow_records.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc')->select('borrow_records.*');
                break;
            case 'borrower_desc':
                $pendingQuery->join('users', 'borrow_records.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc')->select('borrow_records.*');
                break;
            case 'date_asc':
                $pendingQuery->orderBy('borrow_date', 'asc');
                break;
            default:
                $pendingQuery->orderBy('borrow_date', 'desc');
                break;
        }

        $pendingLogs = $pendingQuery->get();

        // --- TRANSACTION HISTORY (completed) ---
        $completedQuery = BorrowRecord::with(['user', 'items.equipment', 'status'])
            ->whereNotNull('actual_return_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $completedQuery->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->withTrashed()->where('name', 'like', "%$search%"))
                    ->orWhereHas('items.equipment', fn($e) => $e->where('equipment_name', 'like', "%$search%"));
            });
        }

        switch ($request->sort) {
            case 'borrower_asc':
                $completedQuery->join('users', 'borrow_records.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc')->select('borrow_records.*');
                break;
            case 'borrower_desc':
                $completedQuery->join('users', 'borrow_records.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc')->select('borrow_records.*');
                break;
            case 'date_asc':
                $completedQuery->orderBy('actual_return_date', 'asc');
                break;
            default:
                $completedQuery->orderBy('actual_return_date', 'desc');
                break;
        }

        $completedLogs = $completedQuery->get();

        return view('admin.dashboard', compact('pendingLogs', 'completedLogs'));
    }

    public function destroyHistory($id)
    {
        $record = BorrowRecord::whereNotNull('actual_return_date')->findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'History record deleted successfully.');
    }

    public function markAsReturned(Request $request, $id)
    {
        $record = BorrowRecord::with('items.equipment')->findOrFail($id);

        if ($record->actual_return_date) {
            return redirect()->back()->with('error', 'This record is already marked as returned.');
        }

        DB::transaction(function () use ($record, $request) {
            foreach ($record->items as $item) {
                if ($item->equipment) {
                    $item->equipment->increment('available_quantity', $item->quantity);
                }
            }
            $record->update([
                'actual_return_date' => now(),
                'status_id' => 2,
                'condition' => $request->condition,
                'remarks' => $request->remarks
            ]);
        });

        return redirect()->back()->with('success', 'Equipment returned and stock updated successfully!');
    }
}
