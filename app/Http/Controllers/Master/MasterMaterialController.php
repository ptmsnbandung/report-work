<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterMaterialController extends Controller
{
    /**
     * Tampilkan daftar master material
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $materials = MasterMaterial::when($search, function ($query, $search) {
            $query->where('nama_material', 'like', "%{$search}%")
                ->orWhere('kode_material', 'like', "%{$search}%")
                ->orWhere('satuan', 'like', "%{$search}%");
        })
        ->orderBy('nama_material')
        ->paginate(10)
        ->withQueryString();

        return view('master.materials.index', compact('materials', 'search'));
    }

    /**
     * Simpan master material baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_material' => ['required', 'string', 'max:50', 'unique:master_materials,kode_material'],
            'nama_material' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok_tersedia' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_material.required' => 'Kode material wajib diisi.',
            'kode_material.unique' => 'Kode material sudah digunakan.',
            'nama_material.required' => 'Nama material wajib diisi.',
            'satuan.required' => 'Satuan material wajib diisi.',
        ]);

        MasterMaterial::create([
            'kode_material' => strtoupper(trim($request->input('kode_material'))),
            'nama_material' => trim($request->input('nama_material')),
            'satuan' => trim($request->input('satuan')),
            'stok_tersedia' => (int) $request->input('stok_tersedia', 0),
            'keterangan' => $request->input('keterangan'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('master.materials.index')
            ->with('success', "Material [{$request->input('nama_material')}] berhasil ditambahkan.");
    }

    /**
     * Update data master material
     */
    public function update(Request $request, MasterMaterial $material): RedirectResponse
    {
        $request->validate([
            'kode_material' => ['required', 'string', 'max:50', 'unique:master_materials,kode_material,' . $material->id],
            'nama_material' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok_tersedia' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_material.required' => 'Kode material wajib diisi.',
            'kode_material.unique' => 'Kode material sudah digunakan.',
            'nama_material.required' => 'Nama material wajib diisi.',
            'satuan.required' => 'Satuan material wajib diisi.',
        ]);

        $material->update([
            'kode_material' => strtoupper(trim($request->input('kode_material'))),
            'nama_material' => trim($request->input('nama_material')),
            'satuan' => trim($request->input('satuan')),
            'stok_tersedia' => (int) $request->input('stok_tersedia', 0),
            'keterangan' => $request->input('keterangan'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $material->is_active,
        ]);

        return redirect()->route('master.materials.index')
            ->with('success', "Data material [{$material->nama_material}] berhasil diperbarui.");
    }

    /**
     * Hapus master material
     */
    public function destroy(MasterMaterial $material): RedirectResponse
    {
        $name = $material->nama_material;
        $material->delete();

        return redirect()->route('master.materials.index')
            ->with('success', "Material [{$name}] berhasil dihapus.");
    }
}
