<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use App\Models\JenisDokumen;
use App\Models\RiwayatStatus;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftaran = Pendaftaran::where('user_id', Auth::id())
            ->with(['programStudi', 'periode'])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.pendaftaran.index', compact('pendaftaran'));
    }

    public function create()
    {
        $periode = PeriodeSeleksi::whereRaw('"status" = \'aktif\'')->first();

        if (!$periode) {
            return view('mahasiswa.pendaftaran.create', [
                'periode'      => null,
                'programStudi' => collect(),
            ]);
        }

        // Cek apakah sudah pernah daftar di periode ini
        $sudahDaftar = Pendaftaran::where('user_id', Auth::id())
            ->where('periode_id', $periode->id)
            ->exists();

        if ($sudahDaftar) {
            return redirect()->route('mahasiswa.pendaftaran.index')
                ->with('error', 'Anda sudah memiliki pendaftaran di periode ini.');
        }

        $programStudi = ProgramStudi::aktif()->orderBy('jenjang')->orderBy('nama')->get();

        return view('mahasiswa.pendaftaran.create', compact('periode', 'programStudi'));
    }

    public function store(Request $request)
    {
        $periode = PeriodeSeleksi::whereRaw('"status" = \'aktif\'')->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        // Cek duplikat user per periode
        $sudahDaftar = Pendaftaran::where('user_id', Auth::id())
            ->where('periode_id', $periode->id)
            ->exists();

        if ($sudahDaftar) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran di periode ini.');
        }

        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'nik'              => [
                'required',
                'regex:/^[0-9]{16}$/',
                Rule::unique('pendaftaran', 'nik'),
            ],
            'no_hp'            => [
                'required',
                'string',
                'max:20',
                Rule::unique('pendaftaran', 'no_hp'),
            ],
            'program_studi_id' => 'required|exists:program_studi,id',
            'asal_daerah'      => 'required|string|max:255',
            'provinsi'         => 'required|string|max:255',
            'is_kip'           => 'boolean',
            'no_kip'           => 'nullable|string|max:50',
        ], [
            'nik.unique'        => 'NIK sudah terdaftar di sistem. Setiap mahasiswa harus memiliki NIK yang berbeda.',
            'nik.regex'         => 'NIK harus terdiri dari 16 digit angka.',
            'nik.required'      => 'NIK wajib diisi.',
            'no_hp.unique'      => 'Nomor HP sudah terdaftar di sistem. Setiap mahasiswa harus memiliki nomor HP yang berbeda.',
            'no_hp.required'    => 'Nomor HP wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'asal_daerah.required' => 'Asal daerah wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
        ]);

        $isKip = $request->boolean('is_kip');

        DB::transaction(function () use ($request, $periode, $isKip) {
            $prefix = 'UKT-' . date('Y') . '-';

            // Cari nomor terakhir SECARA GLOBAL per tahun (bukan per periode)
            $last = Pendaftaran::where('no_pendaftaran', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('no_pendaftaran')
                ->value('no_pendaftaran');

            $lastNum       = $last ? (int) substr($last, -5) : 0;
            $noPendaftaran = $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);

            DB::table('pendaftaran')->insert([
                'user_id'          => Auth::id(),
                'periode_id'       => $periode->id,
                'program_studi_id' => $request->program_studi_id,
                'no_pendaftaran'   => $noPendaftaran,
                'nama_lengkap'     => $request->nama_lengkap,
                'nik'              => $request->nik,
                'no_hp'            => $request->no_hp,
                'asal_daerah'      => $request->asal_daerah,
                'provinsi'         => $request->provinsi,
                'status'           => 'draft',
                'is_kip'           => $isKip ? 'true' : 'false',
                'no_kip'           => $isKip ? $request->no_kip : null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        });

        $pendaftaran = Pendaftaran::where('user_id', Auth::id())
            ->where('periode_id', $periode->id)
            ->latest()
            ->firstOrFail();

        RiwayatStatus::create([
            'pendaftaran_id' => $pendaftaran->id,
            'status_lama'    => null,
            'status_baru'    => 'draft',
            'keterangan'     => 'Pendaftaran dibuat',
            'diubah_oleh'    => Auth::id(),
        ]);

        AuditLogService::log('buat_pendaftaran', 'Pendaftaran', $pendaftaran->id, null, $request->all());

        return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Pendaftaran berhasil dibuat. Silakan lengkapi data ekonomi dan dokumen.');
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $this->authorize('view', $pendaftaran);

        $pendaftaran->load([
            'programStudi',
            'periode',
            'dataEkonomi',
            'dokumen.jenisDokumen',
            'riwayatStatus',
            'banding',
            'verifikasi.reviewer',
        ]);

        return view('mahasiswa.pendaftaran.show', compact('pendaftaran'));
    }

    public function edit(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) {
            return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
                ->with('error', 'Pendaftaran tidak dapat diedit pada status ini.');
        }

        $programStudi = ProgramStudi::aktif()->orderBy('jenjang')->orderBy('nama')->get();

        return view('mahasiswa.pendaftaran.edit', compact('pendaftaran', 'programStudi'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) abort(403);

        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'nik'              => [
                'required',
                'regex:/^[0-9]{16}$/',
                Rule::unique('pendaftaran', 'nik')->ignore($pendaftaran->id),
            ],
            'no_hp'            => [
                'required',
                'string',
                'max:20',
                Rule::unique('pendaftaran', 'no_hp')->ignore($pendaftaran->id),
            ],
            'program_studi_id' => 'required|exists:program_studi,id',
            'asal_daerah'      => 'required|string|max:255',
            'provinsi'         => 'required|string|max:255',
        ], [
            'nik.unique'        => 'NIK sudah terdaftar di sistem. Setiap mahasiswa harus memiliki NIK yang berbeda.',
            'nik.regex'         => 'NIK harus terdiri dari 16 digit angka.',
            'nik.required'      => 'NIK wajib diisi.',
            'no_hp.unique'      => 'Nomor HP sudah terdaftar di sistem. Setiap mahasiswa harus memiliki nomor HP yang berbeda.',
            'no_hp.required'    => 'Nomor HP wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'asal_daerah.required' => 'Asal daerah wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
        ]);

        $dataLama = $pendaftaran->toArray();

        $isKip = $request->boolean('is_kip');

        DB::table('pendaftaran')
            ->where('id', $pendaftaran->id)
            ->update([
                'nama_lengkap'     => $request->nama_lengkap,
                'nik'              => $request->nik,
                'no_hp'            => $request->no_hp,
                'program_studi_id' => $request->program_studi_id,
                'asal_daerah'      => $request->asal_daerah,
                'provinsi'         => $request->provinsi,
                'is_kip'           => $isKip ? 'true' : 'false',
                'no_kip'           => $isKip ? $request->no_kip : null,
                'updated_at'       => now(),
            ]);

        AuditLogService::log('ubah_pendaftaran', 'Pendaftaran', $pendaftaran->id, $dataLama, $request->all());

        return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Pendaftaran berhasil diperbarui.');
    }

    public function submit(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);

        if (!$pendaftaran->bisaDiedit()) {
            return back()->with('error', 'Pendaftaran tidak dapat disubmit.');
        }

        if (!$pendaftaran->dataEkonomi) {
            return back()->with('error', 'Lengkapi data ekonomi terlebih dahulu.');
        }

        $dokumenWajib    = JenisDokumen::whereRaw('"aktif" IS TRUE')
            ->whereRaw('"wajib" IS TRUE')
            ->get();
        $dokumenDiupload = $pendaftaran->dokumen->pluck('jenis_dokumen_id')->toArray();
        $kurang          = $dokumenWajib->filter(fn($j) => !in_array($j->id, $dokumenDiupload));

        if ($kurang->count() > 0) {
            $namaKurang = $kurang->pluck('nama')->join(', ');
            return back()->with('error', "Dokumen wajib belum lengkap: {$namaKurang}");
        }

        $oldStatus = $pendaftaran->status;                        // ← TAMBAH
        $pendaftaran->update(['status' => 'menunggu_verifikasi']);

        RiwayatStatus::create([
            'pendaftaran_id' => $pendaftaran->id,
            'status_lama'    => $oldStatus,
            'status_baru'    => 'menunggu_verifikasi',
            'keterangan'     => 'Pendaftaran disubmit oleh mahasiswa',
            'diubah_oleh'    => Auth::id(),
        ]);

        AuditLogService::log('submit_pendaftaran', 'Pendaftaran', $pendaftaran->id, null, null);

        return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Pendaftaran berhasil disubmit. Menunggu verifikasi.');
    }

    // private function generateNomor(int $periodeId): string
    // {
    //     $tahun = date('Y');
    //     do {
    //         $last  = Pendaftaran::where('periode_id', $periodeId)
    //             ->orderByDesc('no_pendaftaran')
    //             ->value('no_pendaftaran');
    //         $lastNum = $last ? (int) substr($last, -5) : 0;
    //         $nomor   = 'UKT-' . $tahun . '-' . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
    //     } while (Pendaftaran::where('no_pendaftaran', $nomor)->exists());

    //     return $nomor;
    // }
}
