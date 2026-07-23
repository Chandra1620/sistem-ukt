<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Data UKT - Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1e3a5f;
        }

        .header p {
            margin: 4px 0;
            color: #555;
        }

        .filter-info {
            background: #f1f5f9;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e3a5f;
            color: white;
            padding: 6px;
            font-size: 9px;
            text-align: left;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge-kip {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 8px;
        }

        .badge-nonkip {
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 8px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>REKAP DATA UKT - LAPORAN MANAGEMENT</h2>
        <p>{{ $periode->nama ?? 'Semua Periode' }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @if (count($filterInfo) > 0)
        <div class="filter-info">
            <strong>Filter:</strong> {{ implode(' | ', $filterInfo) }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Status KIP</th>
                <th>Status</th>
                <th>Golongan/Sumber</th>
                <th>Nominal UKT</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pendaftaran as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->no_pendaftaran }}</td>
                    <td>{{ $p->nama_lengkap }}</td>
                    <td>{{ $p->programStudi->nama ?? '-' }}</td>
                    <td>
                        @if ($p->is_kip)
                            <span class="badge-kip">KIP</span>
                        @else
                            <span class="badge-nonkip">Non-KIP</span>
                        @endif
                    </td>
                    <td>{{ $p->label_status ?? $p->status }}</td>
                    <td>
                        @if ($p->hasilUkt)
                            {{ $p->is_kip ? 'KIP' : 'Golongan ' . $p->hasilUkt->golongan_final }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->hasilUkt ? 'Rp ' . number_format($p->hasilUkt->nominal_final, 0, ',', '.') : '-' }}</td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total {{ count($pendaftaran) }} data | Sistem UKT
    </div>
</body>

</html>
