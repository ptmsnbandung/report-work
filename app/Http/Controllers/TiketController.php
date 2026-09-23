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
            'closer',
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

        return view('tiket.show', compact('tiket', 'mentionableUsers', 'totalKronologis'));
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
     * Closing tiket gangguan (HelpDesk & Admin)
     */
    public function close(CloseTiketRequest $request, Tiket $tiket): RedirectResponse
    {
        if ($tiket->status === 'CLOSE') {
            return redirect()
                ->route('tiket.show', $tiket->id)
                ->with('error', 'Tiket ini sudah ditutup sebelumnya.');
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
}
