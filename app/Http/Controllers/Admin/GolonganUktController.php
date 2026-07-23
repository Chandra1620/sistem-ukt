<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GolonganUkt;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class GolonganUktController extends Controller
{
    public function index()
    {
        $golongan = GolonganUkt::orderBy('golongan')->get();
        return view('admin.golongan-ukt.index', compact('golongan'));
    }

    public function create()
    {
        return view('admin.golongan-ukt.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'golongan' => 'required|integer|min:1|unique:golongan_ukt,golongan',
            'nominal'  => 'required|numeric|min:0',
            'skor_min' => 'nullable|numeric|min:0|max:1',
            'skor_max' => 'nullable|numeric|min:0|max:1',
        ]);

        $golongan = GolonganUkt::create($request->only([
            'golongan',
            'nominal',
            'skor_min',
            'skor_max'
        ]));

        AuditLogService::log('buat_golongan_ukt', 'GolonganUkt', $golongan->id, null, $request->all());

        return redirect()->route('admin.golongan-ukt.index')
            ->with('success', 'Golongan UKT berhasil ditambahkan.');
    }

    public function edit(GolonganUkt $golonganUkt)
    {
        return view('admin.golongan-ukt.edit', compact('golonganUkt'));
    }

    public function update(Request $request, GolonganUkt $golonganUkt)
    {
        $request->validate([
            'nominal'  => 'required|numeric|min:0',
            'skor_min' => 'nullable|numeric|min:0|max:1',
            'skor_max' => 'nullable|numeric|min:0|max:1',
        ]);

        $dataLama = $golonganUkt->only(['nominal', 'skor_min', 'skor_max']);
        $golonganUkt->update($request->only(['nominal', 'skor_min', 'skor_max']));

        AuditLogService::log('ubah_golongan_ukt', 'GolonganUkt', $golonganUkt->id, $dataLama, $request->all());

        return redirect()->route('admin.golongan-ukt.index')
            ->with('success', 'Golongan UKT berhasil diperbarui.');
    }

    public function destroy(GolonganUkt $golonganUkt)
    {
        if ($golonganUkt->hasilUkt()->count() > 0) {
            return back()->with('error', 'Golongan UKT tidak dapat dihapus karena sudah digunakan.');
        }

        AuditLogService::log('hapus_golongan_ukt', 'GolonganUkt', $golonganUkt->id, $golonganUkt->toArray(), null);
        $golonganUkt->delete();

        return redirect()->route('admin.golongan-ukt.index')
            ->with('success', 'Golongan UKT berhasil dihapus.');
    }
}
