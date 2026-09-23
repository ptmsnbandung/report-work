<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDokumentasiRequest;
use App\Models\Dokumentasi;
use App\Models\Tiket;
use App\Services\DokumentasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DokumentasiController extends Controller
{
    public function __construct(
        protected DokumentasiService $dokumentasiService
    ) {}

    /**
     * Upload dan simpan foto dokumentasi
     */
    public function store(StoreDokumentasiRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        $files = [];
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
        } elseif ($request->hasFile('foto')) {
            $files = [$request->file('foto')];
        }

        $docs = $this->dokumentasiService->upload($tiket, $request->validated(), $files);

        try {
            app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                $tiket,
                count($docs) . ' foto dokumentasi lapangan baru telah diunggah',
                $request->user()
            );
        } catch (\Throwable $e) {
            // Fail silently
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => count($docs) . ' foto dokumentasi berhasil diupload.',
                'data' => $docs,
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiket->id, 'tab' => 'dokumentasi'])
            ->with('success', count($docs) . ' foto dokumentasi berhasil diupload.');
    }

    /**
     * Hapus foto dokumentasi
     */
    public function destroy(Dokumentasi $dokumentasi): RedirectResponse|JsonResponse
    {
        $tiketId = $dokumentasi->id_tiket;
        $this->dokumentasiService->delete($dokumentasi);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Foto dokumentasi berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiketId, 'tab' => 'dokumentasi'])
            ->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}
