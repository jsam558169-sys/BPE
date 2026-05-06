<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // 1. Show the form to create a new Faculty member
    public function create()
    {
        return view('admin.users.create');
    }

    public function index(Request $request)
    {
        $query = User::where('role_id', 2);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        switch ($request->sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // name_asc
                $query->orderBy('name', 'asc');
                break;
        }

        $facultyMembers = $query->get();

        return view('admin.users.index', compact('facultyMembers'));
    }

    public function edit(User $user)
    {
        // We use "Route Model Binding" here (User $user) to fetch the user automatically
        return view('admin.users.edit', compact('user'));
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Check for active borrows (status_id 1 = Borrowed)
        $hasActiveBorrows = $user->borrowRecords()
            ->where('status_id', 1)
            ->exists();

        if ($hasActiveBorrows) {
            return redirect()->back()->with('error', 'Cannot delete this faculty member because they have active borrow records. Please make sure all equipment is returned first.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Faculty member removed successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // The email check ignores the current user's ID so it doesn't error out if they don't change it
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Faculty account updated successfully!');
    }

    // 2. Save the new Faculty member to the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // 2 is the ID for "Faculty" in your roles table
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Faculty account created successfully!');
    }
}
