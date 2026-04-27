<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.view'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $search = $request->get('search');
        $role_id = $request->get('role_id');
        $limit = $request->get('limit', 10);

        $query = User::with('role');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($role_id) {
            $query->where('role_id', $role_id);
        }

        if ($limit === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 1;
        } else {
            $perPage = (int) $limit;
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();
        $roles = \App\Models\Role::all();
        return view('admin.user.users', compact('users', 'roles'));
    }

    public function create()
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.create'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $roles = \App\Models\Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.create'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.edit'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.edit'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->password) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('user.delete'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    public function account()
    {
        $user = auth()->user();
        return view('admin.user.account', compact('user'));
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
