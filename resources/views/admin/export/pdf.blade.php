<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan UKT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p.subtitle {
            text-align: center;
            color: #666;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #1e3a5f;
            color: white;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h2>Laporan Data UKT</h2>
    <p class="subtitle">
        {{ $periode ? $periode->nama : 'Semua Periode' }} |
        Dicetak: {{ now()->format('d M Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pendaftaran</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Skor SAW</th>
                <th>Gol. Rekomendasi</th>
                <th>Gol. Final</th>
                <th>Nominal</th>
                <th>Override</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftaran as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->no_pendaftaran }}</td>
                    <td>{{ $p->nama_lengkap ?? ($p->user?->name ?? '-') }}</td>
                    <td>{{ $p->programStudi?->nama ?? '-' }}</td>
                    <td>{{ $p->hasilSaw?->skor_akhir ? number_format($p->hasilSaw->skor_akhir, 4) : '-' }}</td>
                    <td>{{ $p->hasilUkt?->golongan_rekomendasi ?? '-' }}</td>
                    <td>{{ $p->hasilUkt?->golongan_final ?? '-' }}</td>
                    <td>{{ $p->hasilUkt ? 'Rp ' . number_format($p->hasilUkt->nominal_final, 0, ',', '.') : '-' }}</td>
                    <td>{{ $p->hasilUkt && $p->hasilUkt->is_override ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:#999;">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistem Pendukung Keputusan UKT &copy; {{ date('Y') }}
    </div>
</body>

</html>
