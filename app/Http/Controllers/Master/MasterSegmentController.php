<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterSla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterSegmentController extends Controller
{
    /**
     * Tampilkan daftar master segment backbone
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $segments = MasterSla::when($search, function ($query, $search) {
            $query->where('backbone_segment', 'like', "%{$search}%");
        })
        ->orderBy('backbone_segment')
        ->paginate(10)
        ->withQueryString();

        return view('master.segments.index', compact('segments', 'search'));
    }

    /**
     * Simpan segment backbone baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'backbone_segment' => ['required', 'string', 'max:100', 'unique:master_sla,backbone_segment'],
            'sla_target_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'backbone_segment.required' => 'Nama segment backbone wajib diisi.',
            'backbone_segment.unique' => 'Segment backbone dengan nama ini sudah terdaftar.',
            'sla_target_minutes.required' => 'Target SLA wajib diisi.',
            'sla_target_minutes.min' => 'Target SLA minimal 1 menit.',
        ]);

        MasterSla::create([
            'backbone_segment' => trim($request->input('backbone_segment')),
            'sla_target_minutes' => (int) $request->input('sla_target_minutes'),
        ]);

        return redirect()->route('master.segments.index')
            ->with('success', "Segment backbone [{$request->input('backbone_segment')}] berhasil ditambahkan.");
    }

    /**
     * Update segment backbone
     */
    public function update(Request $request, MasterSla $segment): RedirectResponse
    {
        $request->validate([
            'backbone_segment' => ['required', 'string', 'max:100', 'unique:master_sla,backbone_segment,' . $segment->id],
            'sla_target_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'backbone_segment.required' => 'Nama segment backbone wajib diisi.',
            'backbone_segment.unique' => 'Segment backbone dengan nama ini sudah digunakan.',
            'sla_target_minutes.required' => 'Target SLA wajib diisi.',
            'sla_target_minutes.min' => 'Target SLA minimal 1 menit.',
        ]);

        $segment->update([
            'backbone_segment' => trim($request->input('backbone_segment')),
            'sla_target_minutes' => (int) $request->input('sla_target_minutes'),
        ]);

        return redirect()->route('master.segments.index')
            ->with('success', "Data segment [{$segment->backbone_segment}] berhasil diperbarui.");
    }

    /**
     * Hapus segment backbone
     */
    public function destroy(MasterSla $segment): RedirectResponse
    {
        $name = $segment->backbone_segment;
        $segment->delete();

        return redirect()->route('master.segments.index')
            ->with('success', "Segment backbone [{$name}] berhasil dihapus.");
    }
}
