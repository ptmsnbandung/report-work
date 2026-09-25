<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function __construct(
        protected WebPushService $webPushService
    ) {}

    /**
     * Dapatkan Public Key VAPID untuk pendaftaran browser
     */
    public function vapidPublicKey(): JsonResponse
    {
        $key = $this->webPushService->getPublicKey();
        return response()->json([
            'public_key' => $key,
            'publicKey' => $key,
        ]);
    }

    /**
     * Simpan subscription perangkat/HP pengguna
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['nullable', 'string'],
            'keys.auth' => ['nullable', 'string'],
            'content_encoding' => ['nullable', 'string'],
            'device_type' => ['nullable', 'string', 'in:mobile,desktop,pwa,app'],
            'is_mobile' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $userAgent = $request->userAgent() ?: '';

        // Deteksi apakah subscription berasal dari perangkat HP / Mobile
        $isMobile = $request->boolean('is_mobile');
        if (!$request->has('is_mobile')) {
            $isMobile = (bool) preg_match('/(android|iphone|ipad|ipod|mobile|phone|blackberry|opera mini)/i', $userAgent);
        }

        $deviceType = $validated['device_type'] ?? ($isMobile ? 'mobile' : 'desktop');

        PushSubscription::updateOrCreate(
            [
                'endpoint' => $validated['endpoint'],
            ],
            [
                'user_id'          => $user->id,
                'public_key'       => $validated['keys']['p256dh'] ?? null,
                'auth_token'       => $validated['keys']['auth'] ?? null,
                'content_encoding' => $validated['content_encoding'] ?? 'aesgcm',
                'device_type'      => $deviceType,
                'is_mobile'        => $isMobile,
                'user_agent'       => substr($userAgent, 0, 500),
                'last_active_at'   => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi perangkat berhasil diaktifkan.',
        ]);
    }

    /**
     * Hapus subscription jika user menonaktifkan notifikasi
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi HP dinonaktifkan.',
        ]);
    }

    /**
     * Test kirim notifikasi langsung ke perangkat pengguna yang sedang aktif
     */
    public function testPush(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $this->webPushService->sendToUsers(
            $user,
            '🔔 Test Notifikasi PT MSN',
            'Selamat! Notifikasi langsung ke perangkat Anda berhasil terhubung dengan sukses.',
            route('notifications.index')
        );

        return response()->json([
            'success' => $count > 0,
            'sent_count' => $count,
            'message' => $count > 0 
                ? 'Notifikasi test berhasil dikirim ke perangkat Anda!' 
                : 'Belum ada perangkat terdaftar atau browser menolak izin.',
        ]);
    }
}
