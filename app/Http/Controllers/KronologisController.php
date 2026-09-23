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
     * Dapatkan daftar kronologis tiket (Mendukung AJAX Polling)
     */
    public function index(Request $request, Tiket $tiket): JsonResponse|View
    {
        $timeline = $this->kronologisService->getTimeline($tiket);

        if ($request->wantsJson() || $request->ajax()) {
            $formatted = $timeline->map(function ($krono) {
                return [
                    'id'               => $krono->id,
                    'timestamp'        => $krono->timestamp->toIso8601String(),
                    'formatted_time'   => $krono->timestamp->format('H:i') . ' WIB',
                    'formatted_date'   => $krono->timestamp->translatedFormat('l, d F Y'),
                    'date_key'         => $krono->timestamp->format('Y-m-d'),
                    'user_id'          => $krono->user_id,
                    'user_name'        => $krono->user?->name ?? 'User',
                    'user_role'        => $krono->user?->role_short ?? '-',
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

            return response()->json([
                'success' => true,
                'count'   => $timeline->count(),
                'data'    => $formatted,
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
                return response()->json([
                    'success' => true,
                    'message' => 'Update kronologis berhasil ditambahkan.',
                    'data'    => $kronologis,
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
