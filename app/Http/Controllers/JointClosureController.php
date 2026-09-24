<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJointClosureCoreRequest;
use App\Http\Requests\StoreJointClosureRequest;
use App\Models\Tiket;
use App\Models\TiketJointClosure;
use App\Models\TiketJointClosureCore;
use App\Services\JointClosureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JointClosureController extends Controller
{
    public function __construct(
        protected JointClosureService $jointClosureService
    ) {}

    /**
     * Simpan data Joint Closure / Kabel Sambungan baru
     */
    public function store(StoreJointClosureRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        $jc = $this->jointClosureService->store($tiket, $request->validated(), $request->user()?->id);

        try {
            app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                $tiket,
                "Data Sambungan Kabel & Joint Closure [{$jc->nama_closure}] telah dicatat",
                $request->user()
            );
        } catch (\Throwable $e) {
            // Fail silently
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Joint Closure & Sambungan Kabel berhasil disimpan.',
                'data' => $jc->load('cores'),
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiket->id, 'tab' => 'penanganan'])
            ->with('success', "Data Joint Closure [{$jc->nama_closure}] & Sambungan Kabel berhasil disimpan.");
    }

    /**
     * Tambah satu baris sambungan core ke Joint Closure
     */
    public function addCore(StoreJointClosureCoreRequest $request, TiketJointClosure $jointClosure): RedirectResponse|JsonResponse
    {
        $core = $this->jointClosureService->addCore($jointClosure, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Baris sambungan core berhasil ditambahkan.',
                'data' => $core,
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $jointClosure->id_tiket, 'tab' => 'penanganan'])
            ->with('success', 'Baris sambungan core berhasil ditambahkan.');
    }

    /**
     * Hapus baris sambungan core
     */
    public function deleteCore(TiketJointClosureCore $core): RedirectResponse|JsonResponse
    {
        $tiketId = $core->jointClosure?->id_tiket;
        $this->jointClosureService->deleteCore($core);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Baris sambungan core berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiketId, 'tab' => 'penanganan'])
            ->with('success', 'Baris sambungan core berhasil dihapus.');
    }

    /**
     * Hapus seluruh data Joint Closure
     */
    public function destroy(TiketJointClosure $jointClosure): RedirectResponse|JsonResponse
    {
        $tiketId = $jointClosure->id_tiket;
        $nama = $jointClosure->nama_closure;
        $this->jointClosureService->delete($jointClosure);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Joint Closure [{$nama}] berhasil dihapus.",
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiketId, 'tab' => 'penanganan'])
            ->with('success', "Joint Closure [{$nama}] berhasil dihapus.");
    }
}
