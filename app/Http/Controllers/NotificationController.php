<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman daftar seluruh notifikasi pengguna
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->input('filter', 'all');

        // Otomatis tandai semua notifikasi belum dibaca menjadi terbaca saat halaman dibuka
        $user->unreadNotifications->markAsRead();

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca
     */
    public function markAsRead(string $id, Request $request): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        // Jika ada URL tujuan dalam data notifikasi, redirect langsung ke tiket
        if ($notification && !empty($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Hapus satu notifikasi
     */
    public function destroy(string $id, Request $request): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Hapus semua notifikasi pengguna
     */
    public function destroyAll(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->notifications()->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua riwayat notifikasi berhasil dibersihkan.');
    }

    /**
     * Endpoint API JSON unread count dan 5 notifikasi terbaru untuk Navbar Bell
     */
    public function unreadJson(Request $request): JsonResponse
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        $latest = $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->data['type'] ?? 'INFO',
                    'title' => $notif->data['title'] ?? 'Notifikasi',
                    'message' => $notif->data['message'] ?? '',
                    'icon' => $notif->data['icon'] ?? 'bi-bell',
                    'color' => $notif->data['color'] ?? 'primary',
                    'url' => route('notifications.read', $notif->id),
                    'is_read' => $notif->read_at !== null,
                    'time_ago' => $notif->created_at ? $notif->created_at->diffForHumans() : '',
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latest,
        ]);
    }
}
