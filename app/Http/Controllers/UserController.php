<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $search = $request->get('search');
        $role = $request->get('role');
        $limit = $request->get('limit', 10);

        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($limit === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 1;
        } else {
            $perPage = (int) $limit;
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();
        return view('admin.user.users', compact('users'));
    }

    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,user,super_admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,user,super_admin',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    public function account()
    {
        $user = auth()->user();
        return view('admin.account', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('account')->with('success', 'Account updated successfully.');
    }
}
