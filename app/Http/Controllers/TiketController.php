<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseTiketRequest;
use App\Http\Requests\StoreTiketRequest;
use App\Http\Requests\UpdateTiketRequest;
use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use App\Services\TiketService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class TiketController extends Controller
{
    public function __construct(
        protected TiketService $tiketService
    ) {}

    /**
     * Tampilkan daftar tiket dengan filter, search, & pagination
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->input('status');

        // Untuk role teknisi: jika tidak sedang membuka Tugas Saya (status=AKTIF),
        // maka pada menu History HANYA tampilkan tiket yang sudah closing (CLOSE).
        if ($user && $user->hasRole('teknis') && $status !== 'AKTIF') {
            $status = 'CLOSE';
        }

        $filters = [
            'q'          => $request->input('q'),
            'status'     => $status,
            'segment'    => $request->input('segment'),
            'sla_status' => $request->input('sla_status'),
            'tanggal'    => $request->input('tanggal'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ];

        $tikets = $this->tiketService->getFilteredTiket($filters, 10);
        $summary = $this->tiketService->getTiketSummary();

        // Ambil daftar segment yang tersedia
        $masterSegments = MasterSla::orderBy('backbone_segment')->get();

        return view('tiket.index', compact('tikets', 'summary', 'masterSegments', 'filters'));
    }

    /**
     * Form open tiket baru (HelpDesk & Admin)
     */
    public function create(): View
    {
        $masterSegments = MasterSla::orderBy('backbone_segment')->get();
        $previewNoTiket = $this->tiketService->generateNoTiket();
        $currentDateTime = Carbon::now()->format('Y-m-d\TH:i');

        return view('tiket.create', compact('masterSegments', 'previewNoTiket', 'currentDateTime'));
    }

    /**
     * Simpan tiket baru
     */
    public function store(StoreTiketRequest $request): RedirectResponse
    {
        try {
            $tiket = $this->tiketService->createTiket($request->validated(), $request->user());

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', "Tiket gangguan [{$tiket->no_tiket}] berhasil dibuat dan berstatus OPEN.");
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat tiket: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail tiket
     */
    public function show(Tiket $tiket): View
    {
        $totalKronologis = $tiket->kronologis()->count();
        $initialLimit = 40;

        $tiket->load([
            'creator',
            'resolver',
            'closer',
            'activeStopClock.requester',
            'stopClocks.requester',
            'stopClocks.stopper',
            'handoverShifts.userFrom',
            'handoverShifts.userTo',
            'kronologis' => function ($q) use ($initialLimit, $totalKronologis) {
                if ($totalKronologis > $initialLimit) {
                    $q->orderBy('timestamp', 'desc')
                      ->orderBy('id', 'desc')
                      ->take($initialLimit);
                } else {
                    $q->orderBy('timestamp', 'asc')->orderBy('id', 'asc');
                }
                $q->with('user');
            },
            'resume',
            'materials',
            'titikPerbaikans',
            'manuverCores',
            'jointClosures.cores',
            'jointClosures.creator',
            'dokumentasis',
        ]);

        if ($totalKronologis > $initialLimit) {
            $tiket->setRelation('kronologis', $tiket->kronologis->sortBy('timestamp')->values());
        }

        $mentionableUsers = User::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                return [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'role'       => $u->role_short,
                    'avatar_url' => $u->avatar_url,
                    'initial'    => strtoupper(substr($u->name, 0, 1)),
                ];
            });

        $prerequisites = $tiket->checkClosingPrerequisites();

        return view('tiket.show', compact('tiket', 'mentionableUsers', 'totalKronologis', 'prerequisites'));
    }

    /**
     * Form edit data tiket (Hanya jika status OPEN)
     */
    public function edit(Tiket $tiket): View|RedirectResponse
    {
        if ($tiket->status !== 'OPEN') {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Tiket ini tidak dapat diedit karena sudah dalam status ' . $tiket->status . '.');
        }

        $masterSegments = MasterSla::orderBy('backbone_segment')->get();

        return view('tiket.edit', compact('tiket', 'masterSegments'));
    }

    /**
     * Update data tiket
     */
    public function update(UpdateTiketRequest $request, Tiket $tiket): RedirectResponse
    {
        try {
            $updatedTiket = $this->tiketService->updateTiket($tiket, $request->validated(), $request->user());

            return redirect()
                ->route('tiket.show', $updatedTiket->id)
                ->with('success', "Data tiket [{$updatedTiket->no_tiket}] berhasil diperbarui.");
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui tiket: ' . $e->getMessage());
        }
    }

    /**
     * Tahap 1: Closing Awal oleh Teknisi
     */
    public function closingAwal(Request $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        $request->validate([
            'tipe_penanganan' => 'nullable|string|in:JOINTING_LURUS,MANUVER_CORE,LAINNYA',
            'catatan' => 'nullable|string|max:1000',
            'resolved_at' => 'nullable|date',
            'joint_closure_type' => 'nullable|string|max:100',
            'core_count_jointed' => 'nullable|integer|min:1',
        ]);

        try {
            $resolvedTiket = $this->tiketService->closingAwal($tiket, $request->user(), $request->all());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Closing Awal berhasil disubmit. Menunggu verifikasi HelpDesk NOC.",
                    'data' => $resolvedTiket,
                ]);
            }

            return redirect()
                ->route('tiket.show', $resolvedTiket->id)
                ->with('success', "Closing Awal berhasil! Pekerjaan fisik selesai. Menunggu verifikasi akhir oleh HelpDesk NOC.");
        } catch (InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal closing awal: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal closing awal: ' . $e->getMessage());
        }
    }

    /**
     * Tahap 2: Closing Akhir / Verifikasi oleh HelpDesk & Admin
     */
    public function close(CloseTiketRequest $request, Tiket $tiket): RedirectResponse
    {
        if ($tiket->status === 'CLOSE') {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Tiket ini sudah ditutup sebelumnya.');
        }

        if ($tiket->status !== 'PENDING_VERIFIKASI') {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', "Tiket [{$tiket->no_tiket}] belum dapat ditutup karena teknisi belum melakukan Closing Awal (Status tiket saat ini: {$tiket->status}).");
        }

        try {
            $closedTiket = $this->tiketService->closeTiket($tiket, $request->user(), $request->validated());

            $slaMsg = $closedTiket->sla_status === 'TEPAT'
                ? 'Sesuai SLA target (' . $closedTiket->formatted_mttr . ')'
                : 'Melebihi SLA target (' . $closedTiket->formatted_mttr . ')';

            return redirect()
                ->route('tiket.show', $closedTiket->id)
                ->with('success', "Tiket [{$closedTiket->no_tiket}] berhasil di-CLOSE. MTTR: {$closedTiket->formatted_mttr} - {$slaMsg}.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal menutup tiket: ' . $e->getMessage());
        }
    }

    /**
     * Reject Closing Awal (Kembalikan ke PROSES oleh HelpDesk)
     */
    public function rejectClosingAwal(Request $request, Tiket $tiket): RedirectResponse
    {
        $alasan = $request->input('alasan') ?? $request->input('alasan_reject');

        if (empty($alasan)) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Alasan penolakan / reject wajib diisi.');
        }

        try {
            $this->tiketService->rejectClosingAwal($tiket, $request->user(), $alasan);

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('info', "Verifikasi Closing ditolak. Tiket dikembalikan ke status PROSES.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal mengembalikan tiket: ' . $e->getMessage());
        }
    }

    /**
     * Start Stop Clock
     */
    public function startStopClock(Request $request, Tiket $tiket): RedirectResponse
    {
        $alasanKategori = $request->input('alasan_kategori') ?? $request->input('reason');
        $alasanDetail = $request->input('alasan_detail') ?? $request->input('notes');

        if (empty($alasanKategori)) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Alasan kategori Stop Clock wajib dipilih.');
        }

        $payload = [
            'alasan_kategori' => $alasanKategori,
            'alasan_detail' => $alasanDetail,
            'start_time' => $request->input('start_time'),
        ];

        try {
            $this->tiketService->startStopClock($tiket, $request->user(), $payload);

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Stop Clock aktif! Perhitungan waktu SLA dijeda.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal mengaktifkan Stop Clock: ' . $e->getMessage());
        }
    }

    /**
     * Stop / Resume Stop Clock
     */
    public function stopStopClock(Request $request, Tiket $tiket): RedirectResponse
    {
        try {
            $this->tiketService->stopStopClock($tiket, $request->user());

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Stop Clock dihentikan! Perhitungan waktu SLA dilanjutkan.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal menghentikan Stop Clock: ' . $e->getMessage());
        }
    }

    /**
     * Handover / Oper Shift
     */
    public function handoverShift(Request $request, Tiket $tiket): RedirectResponse
    {
        $shiftFrom = $request->input('shift_from') ?? $request->input('shift_sebelum') ?? 'Shift Sebelumnya';
        $shiftTo = $request->input('shift_to') ?? $request->input('shift_tujuan');
        $catatanHandover = $request->input('catatan_handover') ?? $request->input('status_lapangan');
        if ($request->filled('kendala_pending')) {
            $catatanHandover .= "\nKendala Pending: " . $request->input('kendala_pending');
        }
        if ($request->filled('alokasi_team')) {
            $catatanHandover .= "\nAlokasi Tim: " . $request->input('alokasi_team');
        }

        if (empty($shiftTo) || empty($catatanHandover)) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Shift tujuan dan catatan kondisi lapangan wajib diisi.');
        }

        $payload = [
            'shift_from' => $shiftFrom,
            'shift_to' => $shiftTo,
            'user_to_id' => $request->input('user_to_id'),
            'catatan_handover' => $catatanHandover,
        ];

        try {
            $this->tiketService->handoverShift($tiket, $request->user(), $payload);

            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('success', 'Serah terima / Oper shift tiket berhasil dicatat.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Gagal mencatat oper shift: ' . $e->getMessage());
        }
    }

    /**
     * Real-time AJAX Check Prerequisites for Closing Awal
     */
    public function checkPrerequisites(Tiket $tiket): JsonResponse
    {
        $prereq = $tiket->checkClosingPrerequisites();

        return response()->json($prereq);
    }

    /**
     * Hapus tiket gangguan (Admin Only)
     */
    public function destroy(Request $request, Tiket $tiket): RedirectResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return redirect()
                ->route('tiket.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus tiket.');
        }

        $noTiket = $tiket->no_tiket;

        try {
            $this->tiketService->deleteTiket($tiket, $request->user());

            return redirect()
                ->route('tiket.index')
                ->with('success', "Tiket [{$noTiket}] berhasil dihapus dari sistem.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('tiket.index')
                ->with('error', 'Gagal menghapus tiket: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint JSON preview nomor tiket sesuai tanggal
     */
    public function previewNumber(Request $request): JsonResponse
    {
        $date = $request->filled('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::now();
        $preview = $this->tiketService->generateNoTiket($date);

        return response()->json([
            'no_tiket' => $preview,
        ]);
    }

    /**
     * Update tipe penanganan fisik / core tiket (Jointing Lurus / Manuver Core)
     */
    public function updateTipePenanganan(Request $request, Tiket $tiket): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'tipe_penanganan' => ['required', 'string', 'in:JOINTING_LURUS,MANUVER_CORE,LAINNYA'],
        ]);

        $tiket->update(['tipe_penanganan' => $validated['tipe_penanganan']]);
        if ($tiket->resume) {
            $tiket->resume->update(['tipe_penanganan' => $validated['tipe_penanganan']]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipe penanganan berhasil diperbarui.',
                'tipe_penanganan' => $tiket->tipe_penanganan,
                'label' => $tiket->tipe_penanganan_label,
            ]);
        }

        return redirect()
            ->route('tiket.show', ['tiket' => $tiket->id, 'tab' => 'penanganan'])
            ->with('success', 'Tipe penanganan berhasil diperbarui.');
    }
}
