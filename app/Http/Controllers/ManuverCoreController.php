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
        $validated = $request->validated();
        $coreAsalList = is_array($validated['core_asal']) ? $validated['core_asal'] : [$validated['core_asal']];
        $coreTujuanList = is_array($validated['core_tujuan']) ? $validated['core_tujuan'] : [$validated['core_tujuan']];

        $createdManuvers = [];
        $count = max(count($coreAsalList), count($coreTujuanList));

        for ($i = 0; $i < $count; $i++) {
            $asal = $coreAsalList[$i] ?? $coreAsalList[0];
            $tujuan = $coreTujuanList[$i] ?? $coreTujuanList[0];
            if (empty($asal) || empty($tujuan)) continue;

            $data = $validated;
            $data['core_asal'] = $asal;
            $data['core_tujuan'] = $tujuan;
            $createdManuvers[] = $this->manuverCoreService->addManuver($tiket, $data);
        }

        $lastManuver = !empty($createdManuvers) ? $createdManuvers[0] : null;

        try {
            if ($lastManuver) {
                $totalStr = count($createdManuvers) > 1 ? count($createdManuvers) . ' Core' : "{$lastManuver->core_asal} ➔ {$lastManuver->core_tujuan}";
                app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                    $tiket,
                    "Update Manuver Core: {$lastManuver->titik} ({$totalStr})",
                    $request->user()
                );
            }
        } catch (\Throwable $e) {
            // Fail silently
        }

        $msg = count($createdManuvers) > 1
            ? count($createdManuvers) . ' sambungan manuver core berhasil ditambahkan.'
            : 'Manuver core berhasil ditambahkan.';

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => $lastManuver,
                'items' => $createdManuvers,
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiket->id, 'tab' => 'penanganan'])
            ->with('success', $msg);
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
            ->route('tiket.show', ['tiket' => $tiketId, 'tab' => 'penanganan'])
            ->with('success', 'Manuver core berhasil dihapus.');
    }
}
