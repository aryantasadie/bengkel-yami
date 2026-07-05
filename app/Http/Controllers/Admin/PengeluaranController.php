<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    /**
     * Tampilkan daftar pengeluaran dengan filter.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $pengeluarans = $query->latest('tanggal')->paginate(15)->withQueryString();

        $kategoris = [
            'operasional',
            'gaji',
            'restock_sparepart',
            'restock_logistik',
            'lainnya',
        ];

        if (auth()->user()->role === 'admin') {
            $kategoris = array_diff($kategoris, ['gaji']);
        }

        return view('admin.pengeluaran.index', compact('pengeluarans', 'kategoris'));
    }

    /**
     * Tampilkan form tambah pengeluaran.
     */
    public function create()
    {
        $kategoris = [
            'operasional',
            'gaji',
            'restock_sparepart',
            'restock_logistik',
            'lainnya',
        ];

        if (auth()->user()->role === 'admin') {
            $kategoris = array_diff($kategoris, ['gaji']);
        }

        return view('admin.pengeluaran.create', compact('kategoris'));
    }

    /**
     * Simpan pengeluaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'kategori'   => 'required|string|max:100',
            'deskripsi'  => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'tanggal.required'   => 'Tanggal wajib diisi.',
            'kategori.required'  => 'Kategori wajib dipilih.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'nominal.required'   => 'Nominal wajib diisi.',
        ]);

        if (auth()->user()->role === 'admin' && $request->kategori === 'gaji') {
            return back()->with('error', 'Akses ditolak. Anda tidak dapat membuat pengeluaran untuk gaji.')->withInput();
        }

        Pengeluaran::create($request->only(['tanggal', 'deskripsi', 'kategori', 'nominal', 'keterangan']));

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Tampilkan form edit pengeluaran.
     */
    public function edit($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        if (auth()->user()->role === 'admin' && $pengeluaran->kategori === 'gaji') {
            return redirect()->route('admin.pengeluaran.index')->with('error', 'Akses ditolak. Anda tidak dapat mengedit pengeluaran gaji.');
        }

        $kategoris = [
            'operasional',
            'gaji',
            'restock_sparepart',
            'restock_logistik',
            'lainnya',
        ];

        if (auth()->user()->role === 'admin') {
            $kategoris = array_diff($kategoris, ['gaji']);
        }

        return view('admin.pengeluaran.edit', compact('pengeluaran', 'kategoris'));
    }

    /**
     * Update pengeluaran.
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        if (auth()->user()->role === 'admin' && $pengeluaran->kategori === 'gaji') {
            return redirect()->route('admin.pengeluaran.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'tanggal'    => 'required|date',
            'kategori'   => 'required|string|max:100',
            'deskripsi'  => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if (auth()->user()->role === 'admin' && $request->kategori === 'gaji') {
            return back()->with('error', 'Akses ditolak. Anda tidak dapat membuat pengeluaran untuk gaji.')->withInput();
        }

        $pengeluaran->update($request->only(['tanggal', 'deskripsi', 'kategori', 'nominal', 'keterangan']));

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diupdate.');
    }

    /**
     * Hapus pengeluaran.
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        if (auth()->user()->role === 'admin' && $pengeluaran->kategori === 'gaji') {
            return redirect()->route('admin.pengeluaran.index')->with('error', 'Akses ditolak.');
        }

        $pengeluaran->delete();

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
