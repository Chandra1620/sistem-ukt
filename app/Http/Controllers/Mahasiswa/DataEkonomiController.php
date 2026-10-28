<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\DataEkonomi;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DataEkonomiController extends Controller
{
    private function getKriteria(int $periodeId)
    {
        return Kriteria::where('periode_id', $periodeId)
            ->with(['subKriteria' => fn($q) => $q->orderBy('nilai')])
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            ->filter(fn($k) => $k->subKriteria->count() > 0)
            ->keyBy('kode');
    }

    public function create(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) {
            return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
                ->with('error', 'Data ekonomi tidak dapat diubah pada status ini.');
        }
        $kriteria    = $this->getKriteria($pendaftaran->periode_id);
        $dataEkonomi = $pendaftaran->dataEkonomi;
        return view('mahasiswa.data-ekonomi.create', compact('pendaftaran', 'dataEkonomi', 'kriteria'));
    }

    public function store(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) abort(403);

        $kriteria = $this->getKriteria($pendaftaran->periode_id);

        // Semua kriteria pakai dropdown sub kriteria dengan validasi ketat per kriteria & periode
        $rules    = [];
        $messages = [];
        foreach ($kriteria as $kode => $k) {
            $field         = 'sub_' . strtolower($kode) . '_id';
            $rules[$field] = [
                'required',
                Rule::exists('sub_kriteria', 'id')->where('kriteria_id', $k->id),
            ];
            $messages[$field . '.required'] = "Pilihan {$k->nama} wajib dipilih.";
            $messages[$field . '.exists']   = "Pilihan {$k->nama} tidak valid untuk kriteria ini.";
        }

        $validated = $request->validate($rules, $messages);
        $validated['pendaftaran_id'] = $pendaftaran->id;

        // Map sub kriteria ke field legacy untuk kompatibilitas
        $validated = $this->mapSubKriteriaToFields($validated, $kriteria);

        DataEkonomi::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            $validated
        );

        return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Data ekonomi berhasil disimpan.');
    }

    public function edit(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) {
            return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
                ->with('error', 'Data ekonomi tidak dapat diubah.');
        }
        $dataEkonomi = $pendaftaran->dataEkonomi;
        if (!$dataEkonomi) {
            return redirect()->route('mahasiswa.data-ekonomi.create', $pendaftaran);
        }
        $kriteria = $this->getKriteria($pendaftaran->periode_id);
        return view('mahasiswa.data-ekonomi.edit', compact('pendaftaran', 'dataEkonomi', 'kriteria'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);
        if (!$pendaftaran->bisaDiedit()) abort(403);

        $kriteria = $this->getKriteria($pendaftaran->periode_id);

        $rules    = [];
        $messages = [];
        foreach ($kriteria as $kode => $k) {
            $field         = 'sub_' . strtolower($kode) . '_id';
            $rules[$field] = [
                'required',
                Rule::exists('sub_kriteria', 'id')->where('kriteria_id', $k->id),
            ];
            $messages[$field . '.required'] = "Pilihan {$k->nama} wajib dipilih.";
            $messages[$field . '.exists']   = "Pilihan {$k->nama} tidak valid untuk kriteria ini.";
        }

        $validated = $request->validate($rules, $messages);
        $validated = $this->mapSubKriteriaToFields($validated, $kriteria);

        $pendaftaran->dataEkonomi->update($validated);

        return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
            ->with('success', 'Data ekonomi berhasil diperbarui.');
    }

    private function mapSubKriteriaToFields(array $validated, $kriteria): array
    {
        foreach ($kriteria as $kode => $k) {
            $field = 'sub_' . strtolower($kode) . '_id';
            if (!isset($validated[$field])) continue;

            $sub = SubKriteria::find($validated[$field]);
            if (!$sub) continue;

            match ($kode) {
                // C1: Penghasilan Ayah - ambil range_min sebagai nilai penghasilan
                'C1' => $this->mapPenghasilanAyah($validated, $sub),
                // C2: Penghasilan Ibu
                'C2' => $this->mapPenghasilanIbu($validated, $sub),
                // C3: Jumlah Tanggungan
                'C3' => $validated['jumlah_tanggungan'] = (int) ($sub->range_min ?? $sub->nilai),
                // C4: Tagihan Listrik
                'C4' => $validated['tagihan_listrik']   = (float) ($sub->range_min ?? 0),
                // C5: Pekerjaan Ayah
                'C5' => $validated['pekerjaan_ayah']    = $this->mapPekerjaanAyah($sub->label),
                // C6: Pekerjaan Ibu
                'C6' => $validated['pekerjaan_ibu']     = $this->mapPekerjaanIbu($sub->label),
                // C7: Kepemilikan Rumah
                'C7' => $validated['kepemilikan_rumah'] = $this->mapRumah($sub->label),
                // C8: Kepemilikan Kendaraan
                'C8' => $validated['kepemilikan_kendaraan'] = $this->mapKendaraan($sub->label),
                // C9: Luas Tanah
                'C9' => $validated['luas_tanah']        = (float) ($sub->range_min ?? 0),
                // C10: PBB
                'C10' => $validated['pbb']              = (float) ($sub->range_min ?? 0),
                // C11: Hutang/Cicilan
                'C11' => $validated['hutang_cicilan']   = (float) ($sub->range_min ?? 0),
                // C12: Tagihan Air
                'C12' => $validated['tagihan_air']      = (float) ($sub->range_min ?? 0),
                default => null,
            };
        }

        // Hitung total penghasilan
        $validated['penghasilan_total'] = ($validated['penghasilan_ayah'] ?? 0)
            + ($validated['penghasilan_ibu'] ?? 0);

        return $validated;
    }

    private function mapPenghasilanAyah(array &$validated, SubKriteria $sub): void
    {
        // Ambil nilai tengah range sebagai representasi penghasilan
        $min = (float) ($sub->range_min ?? 0);
        $max = (float) ($sub->range_max ?? $min);
        $validated['penghasilan_ayah'] = $min > 0 ? ($min + $max) / 2 : 0;
    }

    private function mapPenghasilanIbu(array &$validated, SubKriteria $sub): void
    {
        $min = (float) ($sub->range_min ?? 0);
        $max = (float) ($sub->range_max ?? $min);
        $validated['penghasilan_ibu'] = $min > 0 ? ($min + $max) / 2 : 0;
    }

    private function mapRumah(string $label): string
    {
        return match (true) {
            str_contains($label, 'Milik')   => 'milik_sendiri',
            str_contains($label, 'Dinas')   => 'dinas',
            str_contains($label, 'Sewa') ||
                str_contains($label, 'Kontrak') => 'sewa',
            default                          => 'menumpang',
        };
    }

    private function mapKendaraan(string $label): string
    {
        return match (true) {
            str_contains($label, 'Tidak') ||
                str_contains($label, 'tidak')              => 'tidak_ada',
            str_contains($label, 'Motor') &&
                str_contains($label, 'Mobil')              => 'motor_dan_mobil',
            str_contains($label, 'Mobil')              => 'mobil',
            str_contains($label, 'Motor')              => 'motor',
            str_contains($label, 'Lebih') ||
                str_contains($label, '>')                  => 'lebih',
            default                                    => 'tidak_ada',
        };
    }

    private function mapPekerjaanAyah(string $label): string
    {
        return match (true) {
            str_contains($label, 'PNS') ||
                str_contains($label, 'TNI') ||
                str_contains($label, 'Polri') ||
                str_contains($label, 'BUMN')               => 'pns',
            str_contains($label, 'Swasta')             => 'swasta',
            str_contains($label, 'Wiraswasta') ||
                str_contains($label, 'Pengusaha')          => 'wiraswasta',
            str_contains($label, 'Buruh')              => 'buruh',
            str_contains($label, 'Petani') ||
                str_contains($label, 'Nelayan')            => 'petani',
            str_contains($label, 'Meninggal')          => 'meninggal',
            default                                    => 'tidak_bekerja',
        };
    }

    private function mapPekerjaanIbu(string $label): string
    {
        return match (true) {
            str_contains($label, 'PNS') ||
                str_contains($label, 'TNI') ||
                str_contains($label, 'Polri') ||
                str_contains($label, 'BUMN')               => 'pns',
            str_contains($label, 'Swasta')             => 'swasta',
            str_contains($label, 'Wiraswasta') ||
                str_contains($label, 'Pengusaha')          => 'wiraswasta',
            str_contains($label, 'Buruh')              => 'buruh',
            str_contains($label, 'Petani') ||
                str_contains($label, 'Nelayan')            => 'petani',
            str_contains($label, 'IRT') ||
                str_contains($label, 'Ibu Rumah') ||
                str_contains($label, 'Rumah Tangga')       => 'ibu_rumah_tangga',
            str_contains($label, 'Meninggal')          => 'meninggal',
            default                                    => 'tidak_bekerja',
        };
    }
}
