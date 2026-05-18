<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrower::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%")
                    ->orWhere('email',      'like', "%{$search}%");
            });
        }

        match ($request->sort) {
            'name_desc'  => $query->orderBy('last_name', 'desc'),
            'email_asc'  => $query->orderBy('email', 'asc'),
            'email_desc' => $query->orderBy('email', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('last_name', 'asc'),
        };

        $facultyMembers = $query->get();

        return view('admin.users.index', compact('facultyMembers'));
    }

    public function create()
    {
        return view('admin.users.create');
    }



    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'middle_name'    => ['nullable', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:255', 'unique:borrowers,email'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Borrower::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'middle_name'    => $request->middle_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'password'       => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Borrower account created!');
    }

    public function edit($id)
    {
        $user = Borrower::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = Borrower::findOrFail($id);

        $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'middle_name'    => ['nullable', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:255', 'unique:borrowers,email,' . $user->borrower_id . ',borrower_id'],
            'contact_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($request->only([
            'first_name',
            'last_name',
            'middle_name',
            'email',
            'contact_number',
        ]));

        return redirect()->route('admin.users.index')->with('success', 'Borrower account updated!');
    }

    public function destroy($id)
    {
        $borrower = Borrower::findOrFail($id);

        $hasActiveBorrows = $borrower->borrowRecords()
            ->whereDoesntHave('returnRecord')
            ->exists();

        if ($hasActiveBorrows) {
            return redirect()->back()->with('error', 'Cannot delete this borrower — they have active borrow records. Ensure all equipment is returned first.');
        }

        $borrower->delete(); // soft delete
        return redirect()->back()->with('success', 'Borrower removed successfully.');
    }
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        Borrower::findOrFail($id)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Password reset successfully.');
    }
}
