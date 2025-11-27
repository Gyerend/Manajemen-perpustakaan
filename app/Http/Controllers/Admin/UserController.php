<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // <-- BARU: Tambahkan ini

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna. (READ)
     */
    public function index(): View
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru. (CREATE - Form)
     */
    public function create(): View
    {
        $roles = ['admin', 'pegawai', 'mahasiswa'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke database. (CREATE - Store)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'pegawai', 'mahasiswa'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir edit pengguna. (UPDATE - Form)
     */
    public function edit(User $user): View
    {
        $roles = ['admin', 'pegawai', 'mahasiswa'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Memperbarui data pengguna. (UPDATE - Store)
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Validasi email: unik kecuali email milik user ini sendiri
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'pegawai', 'mahasiswa'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna. (DELETE)
     */
    public function destroy(User $user): RedirectResponse
    {
        // Pencegahan: Admin tidak boleh menghapus dirinya sendiri
        if (Auth::user()->id === $user->id) { // <-- Perbaikan: Gunakan Auth::user()
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dihapus.');
    }
}
