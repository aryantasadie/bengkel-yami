<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\JasaKaryawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    /**
     * Tampilkan daftar karyawan.
     */
    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'aktif' ? true : false;
            $query->where('is_active', $isActive);
        }

        $karyawans = $query->latest()->paginate(15)->withQueryString();

        return view('admin.karyawan.index', compact('karyawans'));
    }

    /**
     * Tampilkan form tambah karyawan.
     */
    public function create()
    {
        return view('admin.karyawan.create');
    }

    /**
     * Simpan karyawan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:500',
            'tanggal_masuk' => 'required|date',
            'jabatan'       => 'required|string|max:100',
            'gaji_pokok'    => 'required|numeric|min:0',
            'tunjangan'     => 'nullable|numeric|min:0',
            'is_active'     => 'required|boolean',
            'buat_akun'     => 'nullable|boolean',
            'username'      => 'required_if:buat_akun,1|nullable|string|max:50|unique:users,username',
            'password'      => 'required_if:buat_akun,1|nullable|string|min:6',
        ], [
            'nama.required'    => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'gaji_pokok.required'    => 'Gaji pokok wajib diisi.',
            'username.required_if' => 'Username wajib diisi jika membuat akun.',
            'username.unique'  => 'Username sudah terdaftar.',
            'password.required_if' => 'Password wajib diisi jika membuat akun.',
            'password.min'     => 'Password minimal 6 karakter.',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $karyawan = Karyawan::create([
                'nama'          => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'tanggal_masuk' => $request->tanggal_masuk,
                'jabatan'       => $request->jabatan,
                'gaji_pokok'    => $request->gaji_pokok,
                'tunjangan'     => $request->tunjangan ?? 0,
                'is_active'     => $request->is_active,
            ]);

            if ($request->buat_akun) {
                \App\Models\User::create([
                    'karyawan_id' => $karyawan->id,
                    'nama'        => $karyawan->nama,
                    'username'    => $request->username,
                    'password'    => \Illuminate\Support\Facades\Hash::make($request->password),
                    'role'        => 'karyawan',
                    'is_active'   => $request->is_active,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail karyawan (Monitoring).
     */
    public function show(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // 1. Absensi Karyawan pada periode terpilih (termasuk Alpha)
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Jangan tampilkan hari setelah hari ini jika di bulan berjalan
        if ($tahun == Carbon::now()->year && $bulan == Carbon::now()->month) {
            $endDate = Carbon::today();
        }

        $dbAbsensis = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $absensis = collect();
        for ($date = $endDate->copy(); $date->gte($startDate); $date->subDay()) {
            $dateString = $date->format('Y-m-d');
            
            // Skip hari minggu (opsional, tapi biasa bengkel tutup/libur)
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;

            if ($dbAbsensis->has($dateString)) {
                $absen = $dbAbsensis->get($dateString);
                // Ubah status_view sesuai dengan status aslinya dari DB
                $absen->status_view = ucfirst($absen->status ?? 'hadir');
                $absensis->push($absen);
            } else {
                // Buat object dummy untuk hari absen (alpha)
                $absensis->push((object)[
                    'tanggal' => $dateString,
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                    'status_view' => 'Alpha (Tidak Hadir)'
                ]);
            }
        }

        // Absensi hari ini (Live Status)
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        // 2. Tugas Aktif (Live Tracking - abaikan filter bulan)
        $tugasAktif = JasaKaryawan::with(['pesananJasa.pesanan.customer', 'pesananJasa.jasa'])
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['ditugaskan', 'proses'])
            ->get();

        // 3. Riwayat Tugas Selesai pada periode terpilih
        $riwayatTugas = JasaKaryawan::with(['pesananJasa.pesanan.customer', 'pesananJasa.jasa'])
            ->where('karyawan_id', $karyawan->id)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.karyawan.show', compact('karyawan', 'bulan', 'tahun', 'absensis', 'absensiHariIni', 'tugasAktif', 'riwayatTugas'));
    }

    /**
     * Tampilkan form edit karyawan.
     */
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        return view('admin.karyawan.edit', compact('karyawan'));
    }

    /**
     * Update data karyawan.
     */
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama'          => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:500',
            'tanggal_masuk' => 'required|date',
            'jabatan'       => 'required|string|max:100',
            'gaji_pokok'    => 'required|numeric|min:0',
            'tunjangan'     => 'nullable|numeric|min:0',
            'is_active'     => 'required|boolean',
            'buat_akun'     => 'nullable|boolean',
            'username'      => 'required_if:buat_akun,1|nullable|string|max:50|unique:users,username,' . ($karyawan->user ? $karyawan->user->id : 'NULL'),
            'password'      => 'nullable|string|min:6', // Optional for update
        ], [
            'nama.required'    => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'gaji_pokok.required'    => 'Gaji pokok wajib diisi.',
            'username.required_if' => 'Username wajib diisi jika memperbarui akun.',
            'username.unique'  => 'Username sudah terdaftar.',
            'password.min'     => 'Password minimal 6 karakter.',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $karyawan->update([
                'nama'          => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'tanggal_masuk' => $request->tanggal_masuk,
                'jabatan'       => $request->jabatan,
                'gaji_pokok'    => $request->gaji_pokok,
                'tunjangan'     => $request->tunjangan ?? 0,
                'is_active'     => $request->is_active,
            ]);

            if ($request->buat_akun) {
                if ($karyawan->user) {
                    // Update existing user
                    $userData = [
                        'nama'      => $karyawan->nama,
                        'username'  => $request->username,
                        'is_active' => $request->is_active,
                    ];
                    if ($request->password) {
                        $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
                    }
                    $karyawan->user->update($userData);
                } else {
                    // Create new user if they didn't have one
                    // Require password for new user
                    if (!$request->password) {
                        return back()->withInput()->with('error', 'Password wajib diisi untuk membuat akun baru.');
                    }
                    \App\Models\User::create([
                        'karyawan_id' => $karyawan->id,
                        'nama'        => $karyawan->nama,
                        'username'    => $request->username,
                        'password'    => \Illuminate\Support\Facades\Hash::make($request->password),
                        'role'        => 'karyawan',
                        'is_active'   => $request->is_active,
                    ]);
                }
            } else if ($karyawan->user) {
                // If they have a user but unchecked "buat akun", maybe we just update their status to match
                $karyawan->user->update([
                    'nama' => $karyawan->nama,
                    'is_active' => $request->is_active,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update or create manual absensi by Admin/Owner
     */
    public function updateAbsensi(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $jamMasuk = in_array($request->status, ['alpha', 'sakit']) ? null : $request->jam_masuk;
        $jamKeluar = in_array($request->status, ['alpha', 'sakit']) ? null : $request->jam_keluar;

        Absensi::updateOrCreate(
            [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $jamKeluar,
                'status' => $request->status,
            ]
        );

        return back()->with('success', 'Absensi tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d M Y') . ' berhasil diperbarui.');
    }

    /**
     * Hapus karyawan.
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Cek relasi sebelum hapus
        if ($karyawan->jasaKaryawan()->exists()) {
            return back()->with('error', 'Karyawan tidak bisa dihapus karena memiliki data tugas/pesanan.');
        }

        if ($karyawan->payroll()->exists()) {
            return back()->with('error', 'Karyawan tidak bisa dihapus karena memiliki data payroll.');
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
