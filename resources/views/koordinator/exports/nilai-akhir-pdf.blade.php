<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai Akhir</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .title {
            text-align: center;
            line-height: 1.5;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .line {
            border-top: 1px solid #000;
            margin-top: 4px;
            margin-bottom: 48px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f6b56b;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 4px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .status-lulus {
            color: #15803d;
            font-weight: bold;
        }

        .status-tidak-lulus {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="title">
        <div>DAFTAR NILAI AKHIR</div>
        <div>{{ strtoupper(($praktikum->nama ?? 'PRAKTIKUM FISIKA DASAR') . ' ' . $prodi) }}</div>
        <div>LABORATORIUM LAYANAN DASAR</div>
        <div>TAHUN AJARAN {{ strtoupper(($praktikum->semester ?? '-') . ' ' . ($praktikum->tahun_akademik ?? '-')) }}</div>
    </div>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No.</th>
                <th style="width: 18%;">NIM</th>
                <th>Nama</th>
                <th style="width: 16%;">Nilai Angka</th>
                <th style="width: 13%;">Nilai<br>Huruf</th>
                <th style="width: 16%;">Kelulusan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($nilaiAkhirs as $nilai)
                @php
                    $lulus = (float) ($nilai->nilai_angka ?? 0) >= 60;
                @endphp

                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">{{ $nilai->praktikan->user->nim ?? '-' }}</td>
                    <td>{{ $nilai->praktikan->user->name ?? '-' }}</td>
                    <td class="center">
                        {{ $nilai->nilai_angka !== null ? number_format((float) $nilai->nilai_angka, 2) : '-' }}
                    </td>
                    <td class="center">{{ $nilai->nilai_huruf ?? '-' }}</td>
                    <td class="center {{ $lulus ? 'status-lulus' : 'status-tidak-lulus' }}">
                        {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">
                        Data nilai akhir belum tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>