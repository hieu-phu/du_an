<?php

namespace App\Http\Controllers\WEB;

    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::all();

        return inertia('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        return inertia('Roles/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'description' => $request->description,
        ]);

        return redirect()->route('web.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return inertia('Roles/Show', [
            'role' => $role,
        ]);
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        return inertia('Roles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('web.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return redirect()->route('web.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}