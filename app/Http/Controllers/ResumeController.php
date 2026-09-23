<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Models\Tiket;
use App\Services\ResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResumeController extends Controller
{
    public function __construct(
        protected ResumeService $resumeService
    ) {}

    /**
     * Tampilkan form / editor resume pekerjaan (opsional full page)
     */
    public function show(Tiket $tiket): View
    {
        $tiket->load(['resume', 'materials', 'titikPerbaikans']);
        return view('resume.show', compact('tiket'));
    }

    /**
     * Simpan atau update resume pekerjaan tiket
     */
    public function store(StoreResumeRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        try {
            $resume = $this->resumeService->saveOrUpdateResume($tiket, $request->validated());

            try {
                app(\App\Services\NotificationService::class)->notifyProgresPekerjaan(
                    $tiket,
                    'Pengisian / Update Resume Pekerjaan Lapangan',
                    $request->user()
                );
            } catch (\Throwable $e) {
                // Fail silently
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resume pekerjaan berhasil disimpan.',
                    'data'    => $resume,
                ]);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Resume pekerjaan untuk tiket [' . $tiket->no_tiket . '] berhasil disimpan.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan resume: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan resume: ' . $e->getMessage());
        }
    }

    /**
     * Update resume pekerjaan tiket
     */
    public function update(StoreResumeRequest $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        return $this->store($request, $tiket);
    }
}
