<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterSla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterSlaController extends Controller
{
    /**
     * Tampilkan daftar SLA target per segment
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $slas = MasterSla::when($search, function ($query, $search) {
            $query->where('backbone_segment', 'like', "%{$search}%");
        })
        ->orderBy('sla_target_minutes')
        ->paginate(10)
        ->withQueryString();

        return view('master.sla.index', compact('slas', 'search'));
    }

    /**
     * Simpan SLA target baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'backbone_segment' => ['required', 'string', 'max:100', 'unique:master_sla,backbone_segment'],
            'sla_target_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'backbone_segment.required' => 'Nama segment backbone wajib diisi.',
            'backbone_segment.unique' => 'Segment backbone ini sudah memiliki target SLA.',
            'sla_target_minutes.required' => 'Target SLA wajib diisi.',
            'sla_target_minutes.min' => 'Target SLA minimal 1 menit.',
        ]);

        MasterSla::create([
            'backbone_segment' => trim($request->input('backbone_segment')),
            'sla_target_minutes' => (int) $request->input('sla_target_minutes'),
        ]);

        return redirect()->route('master.sla.index')
            ->with('success', "Target SLA untuk segment [{$request->input('backbone_segment')}] berhasil ditambahkan.");
    }

    /**
     * Update target SLA
     */
    public function update(Request $request, MasterSla $sla): RedirectResponse
    {
        $request->validate([
            'backbone_segment' => ['required', 'string', 'max:100', 'unique:master_sla,backbone_segment,' . $sla->id],
            'sla_target_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'backbone_segment.required' => 'Nama segment backbone wajib diisi.',
            'backbone_segment.unique' => 'Segment backbone sudah digunakan.',
            'sla_target_minutes.required' => 'Target SLA wajib diisi.',
            'sla_target_minutes.min' => 'Target SLA minimal 1 menit.',
        ]);

        $sla->update([
            'backbone_segment' => trim($request->input('backbone_segment')),
            'sla_target_minutes' => (int) $request->input('sla_target_minutes'),
        ]);

        return redirect()->route('master.sla.index')
            ->with('success', "Target SLA segment [{$sla->backbone_segment}] berhasil diperbarui.");
    }

    /**
     * Hapus konfigurasi SLA
     */
    public function destroy(MasterSla $sla): RedirectResponse
    {
        $name = $sla->backbone_segment;
        $sla->delete();

        return redirect()->route('master.sla.index')
            ->with('success', "Konfigurasi SLA segment [{$name}] berhasil dihapus.");
    }
}
