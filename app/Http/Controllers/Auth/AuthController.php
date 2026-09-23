<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi user.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Kombinasi email dan password tidak sesuai.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Validasi akun aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator.',
            ]);
        }

        $request->session()->regenerate();

        // Role-based redirect
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout user dari aplikasi.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout.');
    }

    /**
     * Tampilkan halaman profil pengguna.
     */
    public function profile(): View
    {
        return view('profile.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update profil pengguna.
     */
    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete previous avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->input('new_password'));
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
