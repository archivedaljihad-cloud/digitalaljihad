<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Sholat</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
        body {
            margin: 25px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead th {
            background: #e9ecef;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        table tbody td {
            border: 1px solid #000;
            padding: 7px;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .status-next {
            color: #0d6efd;
            font-weight: bold;
        }
        .status-passed {
            color: #198754;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN JADWAL SHOLAT</h1>
        <h2>
            {{ $setting->nama_aplikasi ?? 'MASJID JAMI\' AL JIHAD' }}
        </h2>
        <p>
            Dicetak pada :
            {{ now()->timezone('Asia/Jakarta')->format('d F Y H:i:s') }}
            WIB
        </p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="38%">Nama Sholat</th>
                <th width="24%">Waktu</th>
                <th width="30%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jadwal as $index => $item)
                @php
                    $now = now()->timezone('Asia/Jakarta')->format('H:i');
                    $isNext = $item->formattedWaktu >= $now;
                @endphp
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>
                    <td class="text-bold">
                        {{ $item->nama_sholat }}
                    </td>
                    <td class="text-center">
                        {{ $item->formattedWaktu }} WIB
                    </td>
                    <td class="text-center">
                        @if ($isNext)
                            <span class="status-next">
                                Mendatang
                            </span>
                        @else
                            <span class="status-passed">
                                Telah Berlalu
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        Tidak ada data jadwal sholat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table> 
    <div class="footer">
        <p>
            Dicetak oleh sistem
            <strong>
                {{ $setting->nama_aplikasi ?? 'MASJID JAMI\' AL JIHAD' }}
            </strong>
        </p>
        <p>
            {{ date('Y') }} &mdash; 2026 brought to you by DKM AL JIHAD Development Project
        </p>
    </div>
</body>
</html>
