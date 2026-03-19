<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\SystemArea;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        $areas = SystemArea::toArray();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'areas' => $areas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'SystemArea' => SystemArea::toArray(),
            'UserRole' => UserRole::toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'system_area' => 'nullable|in:' . implode(',', array_column(SystemArea::toArray(), 'value')),
            'role' => 'required|in:' . implode(',', array_column(UserRole::toArray(), 'value')),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'system_area' => $validated['system_area'],
            'role' => $validated['role'],
            'is_active' => true,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'SystemArea' => SystemArea::toArray(),
            'UserRole' => UserRole::toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class.',id,'.$user->id,
            'system_area' => 'nullable|in:' . implode(',', array_column(SystemArea::toArray(), 'value')),
            'is_active' => 'required|boolean',
            'role' => 'required|in:' . implode(',', array_column(UserRole::toArray(), 'value')),
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'system_area' => $validated['system_area'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        $user->syncRoles([$validated['role']]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', Rules\Password::defaults()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // En vez de borrar, lo desactivamos si es flujo de soft-delete o strict
        $user->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Usuario desactivado correctamente.');
    }
}
