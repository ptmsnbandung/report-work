<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class MasterUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna sistem
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $roleFilter = $request->input('role');

        $users = User::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->when($roleFilter, function ($query, $role) {
            $query->where('role', $role);
        })
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

        $roleStats = [
            'admin' => User::where('role', 'admin')->count(),
            'helpdesk' => User::where('role', 'helpdesk')->count(),
            'teknis' => User::where('role', 'teknis')->count(),
            'sa_cs' => User::where('role', 'sa_cs')->count(),
        ];

        return view('master.users.index', compact('users', 'search', 'roleFilter', 'roleStats'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(['admin', 'helpdesk', 'teknis', 'sa_cs'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan pengguna lain.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => trim($request->input('name')),
            'email' => strtolower(trim($request->input('email'))),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('master.users.index')
            ->with('success', "User [{$user->name}] ({$user->role_label}) berhasil ditambahkan.");
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'helpdesk', 'teknis', 'sa_cs'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        $user->update([
            'name' => trim($request->input('name')),
            'email' => strtolower(trim($request->input('email'))),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $user->is_active,
        ]);

        return redirect()->route('master.users.index')
            ->with('success', "Data user [{$user->name}] berhasil diperbarui.");
    }

    /**
     * Reset password user oleh admin
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return redirect()->route('master.users.index')
            ->with('success', "Password user [{$user->name}] berhasil di-reset.");
    }

    /**
     * Toggle status aktif/nonaktif user
     */
    public function toggleActive(User $user, Request $request): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('master.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('master.users.index')
            ->with('success', "Akun user [{$user->name}] berhasil {$statusStr}.");
    }

    /**
     * Hapus user
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('master.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('master.users.index')
            ->with('success', "User [{$name}] berhasil dihapus dari sistem.");
    }
}
