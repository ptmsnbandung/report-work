<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;
    protected ?string $publicKey;
    protected ?string $privateKey;
    protected ?string $subject;

    public function __construct()
    {
        $this->publicKey = config('services.vapid.public_key', env('VAPID_PUBLIC_KEY'));
        $this->privateKey = config('services.vapid.private_key', env('VAPID_PRIVATE_KEY'));
        $this->subject = config('services.vapid.subject', env('VAPID_SUBJECT', 'mailto:admin@ptmsn.co.id'));

        if ($this->publicKey && $this->privateKey) {
            try {
                $defaultOptions = [
                    'TTL' => 86400,
                    'urgency' => 'high', // Prioritas HIGH memaksa FCM/APNs mengirim seketika tanpa ditahan mode hemat baterai (Doze mode)
                    'batchSize' => 200,
                ];
                $this->webPush = new WebPush([
                    'VAPID' => [
                        'subject' => $this->subject,
                        'publicKey' => $this->publicKey,
                        'privateKey' => $this->privateKey,
                    ],
                ], $defaultOptions);
                $this->webPush->setReuseVAPIDHeaders(true);
            } catch (\Throwable $e) {
                Log::warning('Failed to initialize WebPush service: ' . $e->getMessage());
                $this->webPush = null;
            }
        }
    }

    /**
     * Dapatkan Public Key VAPID untuk pendaftaran di sisi browser (Client)
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Kirim notifikasi Web Push ke single user
     */
    public function sendToUser(User $user, string $title, string $message, string $url = '/', ?string $icon = null): int
    {
        return $this->sendToUsers([$user], $title, $message, $url, $icon);
    }

    /**
     * Kirim notifikasi Web Push ke beberapa user.
     * Aturan prioritas pengiriman:
     * - Jika user memiliki aplikasi mobile / HP yang terdaftar, notifikasi HANYA dikirimkan ke HP (mobile).
     * - Jika user TIDAK memiliki perangkat mobile, notifikasi dikirimkan ke browser web/desktop.
     *
     * @param Collection|array|User $users
     * @param string $title
     * @param string $message
     * @param string $url
     * @param string|null $icon
     * @return int Jumlah notifikasi yang berhasil dikirim
     */
    public function sendToUsers(Collection|array|User $users, string $title, string $message, string $url = '/', ?string $icon = null): int
    {
        if (!$this->webPush) {
            Log::info("[WebPush Disabled/Unconfigured] Target: {$title} - {$message}");
            return 0;
        }

        $userIds = [];
        if ($users instanceof User) {
            $userIds = [$users->id];
        } elseif ($users instanceof Collection) {
            $userIds = $users->pluck('id')->all();
        } elseif (is_array($users)) {
            $userIds = array_map(fn($u) => $u instanceof User ? $u->id : $u, $users);
        }

        if (empty($userIds)) {
            return 0;
        }

        $allSubscriptions = PushSubscription::whereIn('user_id', $userIds)->get();

        if ($allSubscriptions->isEmpty()) {
            return 0;
        }

        // Filter prioritas: Kelompokkan per user_id
        $subscriptions = collect();
        $subsByUser = $allSubscriptions->groupBy('user_id');

        foreach ($subsByUser as $uid => $userSubs) {
            $mobileSubs = $userSubs->filter(function ($sub) {
                if (!empty($sub->is_mobile)) return true;
                if (!empty($sub->device_type) && in_array($sub->device_type, ['mobile', 'pwa', 'app', 'android', 'ios'], true)) return true;
                if (!empty($sub->user_agent) && preg_match('/(android|iphone|ipad|ipod|mobile|phone|blackberry)/i', $sub->user_agent)) return true;
                return false;
            });

            if ($mobileSubs->isNotEmpty()) {
                // User memiliki aplikasi / perangkat mobile: Kirim ke mobile SAJA (jangan kirim ke website desktop)
                $subscriptions = $subscriptions->merge($mobileSubs);
            } else {
                // User belum punya mobile: Kirim ke website desktop
                $subscriptions = $subscriptions->merge($userSubs);
            }
        }

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payloadData = [
            'title' => $title,
            'body' => $message,
            'url' => $url,
            'data' => [
                'url' => $url,
                'timestamp' => now()->timestamp,
            ],
        ];

        // Jika ada foto profil pengirim (WhatsApp-style avatar), sertakan sebagai icon notifikasi
        if (!empty($icon)) {
            $payloadData['icon'] = $icon;
        }

        $payload = json_encode($payloadData);

        $sentCount = 0;
        $staleSubscriptions = [];

        foreach ($subscriptions as $sub) {
            try {
                $webPushSub = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                ]);

                $this->webPush->queueNotification($webPushSub, $payload, ['urgency' => 'high', 'TTL' => 86400]);
            } catch (\Throwable $e) {
                Log::warning("[WebPush Queue Error] Sub ID {$sub->id}: " . $e->getMessage());
            }
        }

        try {
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();

                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    $statusCode = $report->getResponse()?->getStatusCode();
                    Log::warning("[WebPush Delivery Failed] {$statusCode}: {$report->getReason()} on {$endpoint}");

                    // Jika token perangkat sudah kadaluarsa (404/410), tandai untuk dihapus
                    if (in_array($statusCode, [404, 410], true)) {
                        $staleSubscriptions[] = $endpoint;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("[WebPush Flush Exception] " . $e->getMessage());
        }

        // Bersihkan token yang sudah tidak aktif di browser/HP user
        if (!empty($staleSubscriptions)) {
            PushSubscription::whereIn('endpoint', $staleSubscriptions)->delete();
        }

        return $sentCount;
    }
}
