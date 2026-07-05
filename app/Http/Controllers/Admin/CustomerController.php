<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar customer.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Tampilkan form tambah customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Simpan customer baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat'  => 'nullable|string|max:500',
            'diskon_default' => 'nullable|numeric|min:0|max:100',
        ], [
            'nama.required'    => 'Nama customer wajib diisi.',
        ]);

        Customer::create($request->only(['nama', 'no_telp', 'alamat', 'diskon_default']));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail customer + history pesanan.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $pesananHistory = Pesanan::where('customer_id', $id)
            ->with('transaksi')
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', compact('customer', 'pesananHistory'));
    }

    /**
     * Tampilkan form edit customer.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update data customer.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat'  => 'nullable|string|max:500',
            'diskon_default' => 'nullable|numeric|min:0|max:100',
        ], [
            'nama.required'    => 'Nama customer wajib diisi.',
        ]);

        $customer->update($request->only(['nama', 'no_telp', 'alamat', 'diskon_default']));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data customer berhasil diperbarui.');
    }

    /**
     * Hapus customer.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Cek apakah ada pesanan terkait
        if ($customer->pesanan()->exists()) {
            return back()->with('error', 'Customer tidak bisa dihapus karena memiliki data pesanan.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }
}
