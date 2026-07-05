<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', [UserRole::ADMIN, UserRole::OWNER])
            ->orderBy('role')
            ->orderBy('nama')
            ->get();
            
        return view('owner.users.index', compact('users'));
    }

    public function create()
    {
        return view('owner.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'role' => ['required', 'in:' . UserRole::ADMIN . ',' . UserRole::OWNER],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('owner.users.index')
            ->with('success', 'Pengguna ' . $request->nama . ' berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::OWNER])) {
            return redirect()->route('owner.users.index')->with('error', 'Hanya bisa mengedit Admin dan Owner.');
        }

        return view('owner.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::OWNER])) {
            return redirect()->route('owner.users.index')->with('error', 'Hanya bisa mengedit Admin dan Owner.');
        }

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'role' => ['required', 'in:' . UserRole::ADMIN . ',' . UserRole::OWNER],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('owner.users.index')
            ->with('success', 'Data pengguna ' . $user->nama . ' berhasil diperbarui.');
    }
}
