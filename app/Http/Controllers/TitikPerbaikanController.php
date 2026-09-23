<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTitikPerbaikanRequest;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Services\ResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TitikPerbaikanController extends Controller
{
    public function __construct(
        protected ResumeService $resumeService
    ) {}

    /**
     * Tambah titik perbaikan ke tiket
     */
    public function store(StoreTitikPerbaikanRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        try {
            $titik = $this->resumeService->addTitikPerbaikan($tiket, $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Titik perbaikan berhasil ditambahkan.',
                    'data'    => $titik,
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Titik perbaikan [' . $titik->nama_titik . '] berhasil ditambahkan.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan titik perbaikan: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan titik perbaikan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus titik perbaikan
     */
    public function destroy(Request $request, Tiket $tiket, TitikPerbaikan $titikPerbaikan): RedirectResponse|JsonResponse
    {
        try {
            $nama = $titikPerbaikan->nama_titik;
            $this->resumeService->deleteTitikPerbaikan($titikPerbaikan, $request->user());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Titik perbaikan berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', "Titik perbaikan [{$nama}] berhasil dihapus.");
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus titik perbaikan: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal menghapus titik perbaikan: ' . $e->getMessage());
        }
    }
}
