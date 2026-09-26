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
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if ($request->has('redirect')) {
            session()->put('url.intended', $request->input('redirect'));
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi user.
     */
    public function login(LoginRequest $request)
    {
        $loginInput = trim($request->input('email'));
        $password = $request->input('password');
        $remember = $request->has('remember') ? $request->boolean('remember') : true;

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $authenticated = Auth::attempt([$fieldType => $loginInput, 'password' => $password], $remember);

        if (!$authenticated) {
            // Coba field alternatif jika belum cocok (email <-> username)
            $altField = $fieldType === 'email' ? 'username' : 'email';
            $authenticated = Auth::attempt([$altField => $loginInput, 'password' => $password], $remember);
        }

        if (!$authenticated) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kombinasi email/username dan password tidak sesuai.',
                ], 422);
            }
            return back()->withErrors([
                'email' => 'Kombinasi email/username dan password tidak sesuai.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Validasi akun aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator.',
            ]);
        }

        $targetRedirect = $request->input('redirect_url') ?: session('url.intended');

        $request->session()->regenerate();
        $redirectUrl = $targetRedirect ?: route('dashboard');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil, mengalihkan...',
                'redirect_url' => $redirectUrl,
            ]);
        }

        if ($targetRedirect) {
            return redirect()->to($targetRedirect);
        }

        // Default role-based redirect
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
