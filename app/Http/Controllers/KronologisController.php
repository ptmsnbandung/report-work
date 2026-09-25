<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKronologisRequest;
use App\Models\Kronologis;
use App\Models\Tiket;
use App\Services\KronologisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KronologisController extends Controller
{
    public function __construct(
        protected KronologisService $kronologisService
    ) {}

    /**
     * Dapatkan daftar kronologis tiket (Mendukung AJAX Polling & Cursor Pagination)
     */
    public function index(Request $request, Tiket $tiket): JsonResponse|View
    {
        $beforeId = $request->input('before_id') ? (int) $request->input('before_id') : null;
        $afterId = $request->input('after_id') ? (int) $request->input('after_id') : null;
        $limit = $request->input('limit') ? min(100, max(5, (int) $request->input('limit'))) : 40;

        $timeline = $this->kronologisService->getTimeline($tiket, $beforeId, $afterId, $limit);
        $totalCount = $tiket->kronologis()->count();

        if ($request->wantsJson() || $request->ajax()) {
            $currentUserId = auth()->id();
            $viewsKey = "tiket_{$tiket->id}_user_views";
            $views = \Illuminate\Support\Facades\Cache::get($viewsKey, []);

            // Catat kehadiran viewer jika user terautentikasi
            if ($currentUserId) {
                $views[$currentUserId] = [
                    'user_id'   => $currentUserId,
                    'role'      => auth()->user()->role ?? null,
                    'viewed_at' => now()->timestamp,
                ];
                \Illuminate\Support\Facades\Cache::put($viewsKey, $views, now()->addDays(7));
            }

            // Hitung timestamp terakhir kali pengguna lain melihat tiket ini
            $otherViews = collect($views)->where('user_id', '!=', $currentUserId);
            $maxOtherViewTime = $otherViews->pluck('viewed_at')->max() ?? 0;
            $lastOtherKronoTime = $tiket->kronologis()->where('user_id', '!=', $currentUserId)->max('timestamp');
            $lastOtherKronoTimestamp = $lastOtherKronoTime ? \Carbon\Carbon::parse($lastOtherKronoTime)->timestamp : 0;
            $maxReadTimestamp = max((int)$maxOtherViewTime, (int)$lastOtherKronoTimestamp);
            $isTiketClosedOrVerified = in_array($tiket->status, ['CLOSE', 'MENUNGGU_VERIFIKASI', 'RESOLVED']);

            $formatted = $timeline->map(function ($krono) {
                return [
                    'id'               => $krono->id,
                    'timestamp'        => $krono->timestamp->toIso8601String(),
                    'created_at'       => $krono->created_at?->toIso8601String() ?? $krono->timestamp->toIso8601String(),
                    'formatted_time'   => $krono->timestamp->format('H:i') . ' WIB',
                    'formatted_date'   => $krono->timestamp->translatedFormat('l, d F Y'),
                    'date_key'         => $krono->timestamp->format('Y-m-d'),
                    'user_id'          => $krono->user_id,
                    'user_name'        => $krono->user?->name ?? 'User',
                    'user_role'        => $krono->user?->role_short ?? '-',
                    'user_avatar'      => $krono->user?->avatar_url,
                    'kategori'         => $krono->kategori,
                    'kategori_label'   => $krono->kategori_label,
                    'kategori_badge'   => $krono->kategori_badge,
                    'kategori_icon'    => $krono->kategori_icon,
                    'informasi'        => $krono->informasi,
                    'foto_url'         => $krono->foto_url ? asset($krono->foto_url) : null,
                    'latitude'         => $krono->latitude,
                    'longitude'        => $krono->longitude,
                    'has_coordinates'  => $krono->has_coordinates,
                    'google_maps_url'  => $krono->google_maps_url,
                ];
            });

            $oldestInBatch = $timeline->first()?->id;
            $hasMoreOlder = $beforeId 
                ? $tiket->kronologis()->where('id', '<', $oldestInBatch ?? 0)->exists()
                : ($totalCount > $timeline->count());

            return response()->json([
                'success'               => true,
                'total_count'           => $totalCount,
                'count'                 => $formatted->count(),
                'has_more'              => $hasMoreOlder,
                'oldest_id'             => $oldestInBatch,
                'max_read_timestamp'    => $maxReadTimestamp,
                'is_closed_or_verified' => $isTiketClosedOrVerified,
                'data'                  => $formatted,
            ]);
        }

        return view('kronologis.index', compact('tiket', 'timeline'));
    }

    /**
     * Simpan update kronologis baru
     */
    public function store(StoreKronologisRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        try {
            $foto = $request->file('foto');
            $kronologis = $this->kronologisService->addKronologis($tiket, $request->validated(), $request->user(), $foto);

            if ($request->wantsJson() || $request->ajax()) {
                $kronologis->load('user');
                return response()->json([
                    'success' => true,
                    'message' => 'Update kronologis berhasil ditambahkan.',
                    'data'    => [
                        'id'               => $kronologis->id,
                        'timestamp'        => $kronologis->timestamp->toIso8601String(),
                        'created_at'       => $kronologis->created_at?->toIso8601String() ?? $kronologis->timestamp->toIso8601String(),
                        'formatted_time'   => $kronologis->timestamp->format('H:i') . ' WIB',
                        'formatted_date'   => $kronologis->timestamp->translatedFormat('l, d F Y'),
                        'date_key'         => $kronologis->timestamp->format('Y-m-d'),
                        'user_id'          => $kronologis->user_id,
                        'user_name'        => $kronologis->user?->name ?? 'User',
                        'user_role'        => $kronologis->user?->role_short ?? '-',
                        'user_avatar'      => $kronologis->user?->avatar_url,
                        'kategori'         => $kronologis->kategori,
                        'kategori_label'   => $kronologis->kategori_label,
                        'kategori_badge'   => $kronologis->kategori_badge,
                        'kategori_icon'    => $kronologis->kategori_icon,
                        'informasi'        => $kronologis->informasi,
                        'foto_url'         => $kronologis->foto_url ? asset($kronologis->foto_url) : null,
                        'latitude'         => $kronologis->latitude,
                        'longitude'        => $kronologis->longitude,
                        'has_coordinates'  => $kronologis->has_coordinates,
                        'google_maps_url'  => $kronologis->google_maps_url,
                    ],
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Update kronologis [' . $kronologis->kategori . '] berhasil ditambahkan ke timeline.')
                ->with('new_krono_id', $kronologis->id);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan kronologis: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kronologis: ' . $e->getMessage());
        }
    }

    /**
     * Update catatan kronologis (Admin atau Pembuat Pesan - Maksimal 5 Menit)
     */
    public function update(Request $request, Tiket $tiket, Kronologis $kronologis): RedirectResponse|JsonResponse
    {
        if (!$request->user()->hasRole('admin') && $kronologis->user_id !== $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengedit catatan ini.',
                ], 403);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengedit catatan ini.');
        }

        // Batas waktu edit: hanya bisa diedit dalam waktu 5 menit setelah pesan dibuat
        $sentTime = $kronologis->created_at ?: $kronologis->timestamp;
        if ($sentTime && $sentTime->addMinutes(5)->isPast()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas waktu edit pesan telah habis (hanya dapat diedit dalam waktu 5 menit setelah terkirim).',
                ], 422);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Batas waktu edit pesan telah habis (hanya dapat diedit dalam waktu 5 menit setelah terkirim).');
        }

        $validated = $request->validate([
            'informasi' => 'required|string|max:5000',
        ]);

        try {
            $kronologis->update([
                'informasi' => $validated['informasi'],
            ]);

            $kronologis->load('user');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan kronologis berhasil diperbarui.',
                    'data'    => [
                        'id'               => $kronologis->id,
                        'timestamp'        => $kronologis->timestamp->toIso8601String(),
                        'created_at'       => $kronologis->created_at?->toIso8601String() ?? $kronologis->timestamp->toIso8601String(),
                        'formatted_time'   => $kronologis->timestamp->format('H:i') . ' WIB',
                        'formatted_date'   => $kronologis->timestamp->translatedFormat('l, d F Y'),
                        'date_key'         => $kronologis->timestamp->format('Y-m-d'),
                        'user_id'          => $kronologis->user_id,
                        'user_name'        => $kronologis->user?->name ?? 'User',
                        'user_role'        => $kronologis->user?->role_short ?? '-',
                        'user_avatar'      => $kronologis->user?->avatar_url,
                        'kategori'         => $kronologis->kategori,
                        'kategori_label'   => $kronologis->kategori_label,
                        'kategori_badge'   => $kronologis->kategori_badge,
                        'kategori_icon'    => $kronologis->kategori_icon,
                        'informasi'        => $kronologis->informasi,
                        'foto_url'         => $kronologis->foto_url ? asset($kronologis->foto_url) : null,
                        'latitude'         => $kronologis->latitude,
                        'longitude'        => $kronologis->longitude,
                        'has_coordinates'  => $kronologis->has_coordinates,
                        'google_maps_url'  => $kronologis->google_maps_url,
                    ],
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Pesan kronologis berhasil diperbarui.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kronologis: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kronologis: ' . $e->getMessage());
        }
    }

    /**
     * Hapus catatan kronologis (Admin Only)
     */
    public function destroy(Request $request, Tiket $tiket, Kronologis $kronologis): RedirectResponse|JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya Admin yang dapat menghapus kronologis.',
                ], 403);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Akses ditolak. Hanya Admin yang dapat menghapus kronologis.');
        }

        try {
            $this->kronologisService->deleteKronologis($kronologis, $request->user());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catatan kronologis berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Catatan kronologis berhasil dihapus.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kronologis: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal menghapus kronologis: ' . $e->getMessage());
        }
    }
}
