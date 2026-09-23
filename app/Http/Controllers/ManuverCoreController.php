<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManuverCoreRequest;
use App\Models\ManuverCore;
use App\Models\Tiket;
use App\Services\ManuverCoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ManuverCoreController extends Controller
{
    public function __construct(
        protected ManuverCoreService $manuverCoreService
    ) {}

    /**
     * Simpan record manuver core
     */
    public function store(StoreManuverCoreRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        $manuver = $this->manuverCoreService->addManuver($tiket, $request->validated());

        try {
            app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                $tiket,
                "Update Manuver Core: {$manuver->segment_tujuan} (Tube {$manuver->tube} / Core {$manuver->core})",
                $request->user()
            );
        } catch (\Throwable $e) {
            // Fail silently
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Manuver core berhasil ditambahkan.',
                'data' => $manuver,
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiket->id, 'tab' => 'manuver'])
            ->with('success', 'Manuver core berhasil ditambahkan.');
    }

    /**
     * Hapus record manuver core
     */
    public function destroy(ManuverCore $manuverCore): RedirectResponse|JsonResponse
    {
        $tiketId = $manuverCore->id_tiket;
        $this->manuverCoreService->deleteManuver($manuverCore);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Manuver core berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiketId, 'tab' => 'manuver'])
            ->with('success', 'Manuver core berhasil dihapus.');
    }
}
