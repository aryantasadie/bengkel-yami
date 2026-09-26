<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\KategoriJasa;
use Illuminate\Http\Request;

class KategoriJasaController extends Controller {
    public function index() {
        $kategoris = KategoriJasa::withCount('jasa')->get();
        return view('admin.kategori_jasa.index', compact('kategoris'));
    }
    public function create() {
        return view('admin.kategori_jasa.create');
    }
    public function store(Request $request) {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        KategoriJasa::create($request->all());
        return redirect()->route('admin.kategori_jasa.index')->with('success', 'Kategori Jasa berhasil ditambahkan');
    }
    public function edit(KategoriJasa $kategoriJasa) {
        return view('admin.kategori_jasa.edit', compact('kategoriJasa'));
    }
    public function update(Request $request, KategoriJasa $kategoriJasa) {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        $kategoriJasa->update($request->all());
        return redirect()->route('admin.kategori_jasa.index')->with('success', 'Kategori Jasa berhasil diperbarui');
    }
    public function destroy(KategoriJasa $kategoriJasa) {
        $kategoriJasa->delete();
        return redirect()->route('admin.kategori_jasa.index')->with('success', 'Kategori Jasa berhasil dihapus');
    }
}
