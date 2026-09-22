<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();
        $setting = AppSetting::first();

        return view('users.index', compact('users', 'setting'));
    }

    public function create()
    {
        try {
            $roles = Role::orderBy('name')->get();
            if ($roles->isEmpty()) {
                foreach (['admin', 'petugas', 'bendahara', 'user'] as $roleName) {
                    Role::firstOrCreate(['name' => $roleName]);
                }
                $roles = Role::orderBy('name')->get();
            }
        } catch (\Throwable $e) {
            $roles = collect();
        }

        $setting = AppSetting::first();

        return view('users.create', compact('roles', 'setting'));
    }

    public function edit(User $user)
    {
        try {
            $roles = Role::orderBy('name')->get();
            if ($roles->isEmpty()) {
                foreach (['admin', 'petugas', 'bendahara', 'user'] as $roleName) {
                    Role::firstOrCreate(['name' => $roleName]);
                }
                $roles = Role::orderBy('name')->get();
            }
        } catch (\Throwable $e) {
            $roles = collect();
        }

        $setting = AppSetting::first();

        return view('users.edit', compact('user', 'roles', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'last_name' => $request->last_name ?? '',
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $data = $request->only(['name', 'email', 'role_id']);
        $data['last_name'] = $request->last_name ?? '';

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Proteksi: Admin tidak bisa menghapus akun admin lain
        // Admin tidak bisa menghapus akun sendiri
        // Hanya petugas yang bisa dihapus
        
        if ($user->role && in_array(strtolower($user->role->name), ['admin', 'superadmin'])) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun Administrator!');
        }
        
        if (Auth::user()->id == $user->id) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun sendiri!');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }
}