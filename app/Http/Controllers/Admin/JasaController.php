<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class JasaController extends Controller
{
    /**
     * Tampilkan daftar jasa.
     */
    public function index(Request $request)
    {
        $query = Jasa::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_jasa', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $jasas = $query->latest()->paginate(15)->withQueryString();

        return view('admin.jasa.index', compact('jasas'));
    }

    /**
     * Tampilkan form tambah jasa.
     */
    public function create()
    {
        $spareparts = Sparepart::all();

        return view('admin.jasa.create', compact('spareparts'));
    }

    /**
     * Simpan jasa baru + Bill of Materials (pivot sparepart).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jasa'   => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:1000',
            'spareparts'          => 'nullable|array',
            'spareparts.*.id'     => 'required_with:spareparts|exists:sparepart,id',
            'spareparts.*.jumlah' => 'required_with:spareparts|integer|min:1',
        ], [
            'nama_jasa.required' => 'Nama jasa wajib diisi.',
            'harga.required'     => 'Harga wajib diisi.',
        ]);

        $jasa = Jasa::create([
            'nama_jasa' => $request->nama_jasa,
            'harga'     => $request->harga,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        // Sync sparepart (Bill of Materials)
        if ($request->filled('spareparts')) {
            $sparepartData = [];
            foreach ($request->spareparts as $sp) {
                $sparepartData[$sp['id']] = ['qty_default' => $sp['jumlah']];
            }
            $jasa->spareparts()->sync($sparepartData);
        }

        return redirect()->route('admin.jasa.index')
            ->with('success', 'Jasa berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail jasa.
     */
    public function show($id)
    {
        $jasa = Jasa::with('spareparts')->findOrFail($id);

        return view('admin.jasa.show', compact('jasa'));
    }

    /**
     * Tampilkan form edit jasa.
     */
    public function edit($id)
    {
        $jasa = Jasa::with('spareparts')->findOrFail($id);
        $spareparts = Sparepart::all();

        return view('admin.jasa.edit', compact('jasa', 'spareparts'));
    }

    /**
     * Update data jasa + sync sparepart.
     */
    public function update(Request $request, $id)
    {
        $jasa = Jasa::findOrFail($id);

        $request->validate([
            'nama_jasa'   => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:1000',
            'spareparts'          => 'nullable|array',
            'spareparts.*.id'     => 'required_with:spareparts|exists:sparepart,id',
            'spareparts.*.jumlah' => 'required_with:spareparts|integer|min:1',
        ], [
            'nama_jasa.required' => 'Nama jasa wajib diisi.',
            'harga.required'     => 'Harga wajib diisi.',
        ]);

        $jasa->update([
            'nama_jasa' => $request->nama_jasa,
            'harga'     => $request->harga,
            'deskripsi' => $request->deskripsi,
        ]);

        // Sync sparepart (Bill of Materials)
        $sparepartData = [];
        if ($request->filled('spareparts')) {
            foreach ($request->spareparts as $sp) {
                $sparepartData[$sp['id']] = ['qty_default' => $sp['jumlah']];
            }
        }
        $jasa->spareparts()->sync($sparepartData);

        return redirect()->route('admin.jasa.index')
            ->with('success', 'Data jasa berhasil diperbarui.');
    }

    /**
     * Nonaktifkan jasa (soft deactivate).
     */
    public function destroy($id)
    {
        $jasa = Jasa::findOrFail($id);

        $jasa->update(['is_active' => false]);

        return redirect()->route('admin.jasa.index')
            ->with('success', 'Jasa berhasil dinonaktifkan.');
    }
}
