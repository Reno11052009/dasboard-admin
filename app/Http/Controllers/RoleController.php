<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Define all available permissions in the system
    private $availablePermissions = [
        'product' => ['product.view', 'product.create', 'product.edit', 'product.delete'],
        'order'   => ['order.view', 'order.edit', 'order.delete'],
        'user'    => ['user.view', 'user.create', 'user.edit', 'user.delete'],
        'role'    => ['role.view', 'role.create', 'role.edit', 'role.delete'],
    ];

    public function index(Request $request)
    {
        // Only Super Admin or someone with role.view permission can access
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.view'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $roles = Role::latest()->paginate(10);
        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.create'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $availablePermissions = $this->availablePermissions;
        return view('admin.role.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.create'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        Role::create([
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.edit'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $role = Role::findOrFail($id);
        
        // Prevent editing Super Admin to avoid lockouts
        if ($role->name === 'Super Admin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('roles')->with('error', 'Anda tidak dapat mengedit Super Admin.');
        }

        $availablePermissions = $this->availablePermissions;
        return view('admin.role.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.edit'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ]);

        $role->name = $request->name;
        
        // If updating Super Admin, ensure they don't lose permissions
        if ($role->name === 'Super Admin') {
            $allPerms = [];
            foreach ($this->availablePermissions as $group => $perms) {
                $allPerms = array_merge($allPerms, $perms);
            }
            $role->permissions = $allPerms;
        } else {
            $role->permissions = $request->permissions ?? [];
        }

        $role->save();

        return redirect()->route('roles')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('role.delete'))) {
            return redirect('/')->with('error', 'Akses ditolak');
        }

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()->route('roles')->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        $role->delete();

        return redirect()->route('roles')->with('success', 'Role deleted successfully.');
    }
}
