<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\Material;
use App\Models\Tiket;
use App\Services\ResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(
        protected ResumeService $resumeService
    ) {}

    /**
     * Tambah material ke tiket
     */
    public function store(StoreMaterialRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        try {
            $material = $this->resumeService->addMaterial($tiket, $request->validated());

            try {
                app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                    $tiket,
                    "Penambahan Material: {$material->nama_material} ({$material->jumlah} {$material->satuan})",
                    $request->user()
                );
            } catch (\Throwable $e) {
                // Fail silently
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material berhasil ditambahkan.',
                    'data'    => $material,
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Material [' . $material->nama_material . ' - ' . $material->jumlah . ' ' . $material->satuan . '] berhasil ditambahkan.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan material: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan material: ' . $e->getMessage());
        }
    }

    /**
     * Hapus material
     */
    public function destroy(Request $request, Tiket $tiket, Material $material): RedirectResponse|JsonResponse
    {
        try {
            $nama = $material->nama_material;
            $this->resumeService->deleteMaterial($material, $request->user());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Material berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', "Material [{$nama}] berhasil dihapus.");
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus material: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal menghapus material: ' . $e->getMessage());
        }
    }
}
