<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reports</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4B49AC;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #4B49AC;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #666;
        }
        .summary-box {
            display: inline-block;
            width: 30%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-right: 2%;
            text-align: center;
            margin-bottom: 15px;
        }
        .summary-box h4 {
            margin: 0;
            font-size: 13px;
            color: #4B49AC;
        }
        .summary-box p {
            margin: 5px 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table thead {
            background-color: #4B49AC;
            color: #fff;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 7px 10px;
            text-align: left;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Ringkasan Data Buku per Kategori</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div style="margin-bottom: 15px;">
        <div class="summary-box">
            <h4>Total Kategori</h4>
            <p>{{ $totalKategori }}</p>
        </div>
        <div class="summary-box">
            <h4>Total Buku</h4>
            <p>{{ $totalBuku }}</p>
        </div>
        <div class="summary-box">
            <h4>Rata-rata Buku/Kategori</h4>
            <p>{{ $totalKategori > 0 ? round($totalBuku / $totalKategori, 1) : 0 }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Jumlah Buku</th>
                <th style="width: 40%;">Daftar Judul</th>
                <th style="width: 30%;">Daftar Pengarang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report['kategori'] }}</td>
                    <td>{{ $report['jumlah_buku'] }}</td>
                    <td>{{ $report['daftar_judul'] }}</td>
                    <td>{{ $report['daftar_pengarang'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem.
    </div>
</body>
</html>
