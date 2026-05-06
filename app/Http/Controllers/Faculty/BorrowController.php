<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\BorrowRecord;
use App\Models\BorrowRecordItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index(Request $request) // Added Request $request here
    {
        // 1. Start a query instead of getting results immediately
        $query = Equipment::query();

        // 2. Handle Search (Filters by name)
        if ($request->filled('search')) {
            $query->where('equipment_name', 'like', '%' . $request->search . '%');
        }

        // 3. Handle Sorting
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
            case 'name_asc':
            default:
                $query->orderBy('equipment_name', 'asc');
                break;
        }

        // 4. Fetch the filtered results
        // Note: Removed the > 0 check so users can see "Out of Stock" items 
        // to match your Blade @if($item->available_quantity > 0) logic.
        $equipment = $query->get();

        // Get only the logged-in user's records
        $myHistory = BorrowRecord::with(['items.equipment', 'status'])
            ->where('user_id', Auth::id())
            ->get();

        return view('faculty.borrow', compact('equipment', 'myHistory'));
    }

    public function history(Request $request) // Add Request $request
    {
        $query = BorrowRecord::with(['items.equipment', 'status'])
            ->where('user_id', Auth::id());

        // 1. HANDLE SEARCH (Search by Ref # or Equipment Name)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%") // Search Ref #
                    ->orWhereHas('items.equipment', function ($itemQuery) use ($searchTerm) {
                        $itemQuery->where('equipment_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // 2. HANDLE SORTING
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('borrow_date', 'asc');
                break;
            case 'status':
                $query->orderBy('status_id', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('borrow_date', 'desc'); // Newest first is best for history
                break;
        }

        $myHistory = $query->get();

        return view('faculty.history', compact('myHistory'));
    }

    public function create()
    {
        // Fetch faculties (role_id 2) and available equipment
        $faculties = \App\Models\User::where('role_id', 2)->get();
        $equipments = Equipment::where('available_quantity', '>', 0)->get();

        return view('faculty.borrow.create', compact('faculties', 'equipments'));
    }

    public function store(Request $request)
    {
        // 1. Add return_time to validation
        $request->validate([
            'expected_return_date' => 'required|date|after_or_equal:today',
            'return_time'          => 'required', // Added this
            'items'                => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:equipment,equipment_id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // COMBINE DATE AND TIME:
                // This merges '2023-10-25' and '14:30' into '2023-10-25 14:30:00'
                $combinedDateTime = $request->expected_return_date . ' ' . $request->return_time;

                // Create the Header Record
                $borrow = BorrowRecord::create([
                    'user_id' => Auth::id(),
                    'status_id' => 1,
                    'borrow_date' => now(),
                    'expected_return_date' => $combinedDateTime, // Use combined value
                ]);

                foreach ($request->items as $itemData) {
                    $equipment = Equipment::lockForUpdate()->findOrFail($itemData['equipment_id']);

                    if ($equipment->available_quantity < $itemData['quantity']) {
                        throw new \Exception("Insufficient stock for {$equipment->equipment_name}.");
                    }

                    BorrowRecordItem::create([
                        'borrow_record_id' => $borrow->borrow_id ?? $borrow->id,
                        'equipment_id' => $equipment->equipment_id,
                        'quantity' => $itemData['quantity'],
                    ]);

                    // NO DECREMENT HERE - The Trigger handles it!
                }
            });

            return redirect()->route('faculty.history')->with('Success', 'Equipment issued successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
