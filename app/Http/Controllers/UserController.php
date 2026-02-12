<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtro de búsqueda por nombre o email
        // Se agrega ->where('id', '!=', 1) para excluir al usuario soporte
        $users = User::query()
            ->where('id', '!=', 1)
            ->with('roles') // Eager loading de roles
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function($q) use ($search) {
                     $q->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Enviamos los roles disponibles a la vista si es necesario dinámicamente,
        // aunque el frontend ya tendrá las opciones fijas solicitadas.
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|in:Super admin,Vendedor,Diseñador', // Validación estricta de roles
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol usando Spatie
        // Asegúrate de que los roles existan en tu tabla 'roles'
        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente con el rol de ' . $request->role . '.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string|in:Super admin,Vendedor,Diseñador',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Si se envía password, actualizarlo
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Sincronizar roles
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Toggle the status (is_active) of the specified user.
     */
    public function toggleStatus(User $user)
    {
        // Prevenir desactivarse a sí mismo por seguridad
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$status} correctamente.");
    }
}