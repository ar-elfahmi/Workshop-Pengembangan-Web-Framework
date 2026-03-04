{{--
    Template PDF Label Tom & Jerry 108 (T&J 108)

    Template ini di-render oleh DomPDF untuk menghasilkan PDF label
    dengan layout grid presisi pada kertas T&J 108 (±127mm x 205mm).

    Variabel yang diterima:
    - $config: Array konfigurasi dimensi kertas dan label
    - $pages: Array halaman, masing-masing berisi 'labels' (map "baris-kolom" => teks)
    - $totalPages: Total jumlah halaman

    Spesifikasi Kertas T&J 108:
    - Ukuran kertas: 127mm x 205mm (Custom Size 12.7cm x 20.5cm)
    - Layout: 4 kolom x 10 baris = 40 label per lembar
    - Margin: atas/bawah ±12mm, kiri/kanan ±6mm
    - Gap: horizontal 2mm, vertikal 1mm
    - Stiker: 18mm x 38mm (ukuran nominal pada kemasan)

    CATATAN PENTING:
    - Semua dimensi menggunakan satuan mm (milimeter)
    - DomPDF mendukung unit mm dalam CSS
    - Jangan tambahkan scaling atau transform apapun
    - @page margin: 0 agar DomPDF tidak menambah margin sendiri
    - Pengaturan printer: Custom Size, scaling 100%
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label T&J 108</title>
    <style>
        /*
         * === PENGATURAN HALAMAN ===
         * Size kustom T&J 108: 127mm x 205mm, margin 0 agar posisi label presisi.
         * Semua positioning dihitung dari tepi kertas.
         * Di pengaturan printer, gunakan Custom Size 12.7cm x 20.5cm.
         */
        @page {
            size: {{ $config['paper_width'] }}mm {{ $config['paper_height'] }}mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            /* Prevent browser auto-scaling */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /*
         * === CONTAINER HALAMAN ===
         * Setiap .page merepresentasikan 1 lembar kertas T&J 108.
         * page-break-after memastikan setiap halaman dimulai di lembar baru.
         */
        .page {
            width: {{ $config['paper_width'] }}mm;
            height: {{ $config['paper_height'] }}mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /*
         * === TABEL LABEL ===
         * Menggunakan tabel HTML untuk layout grid yang reliable di DomPDF.
         * border-collapse: separate + border-spacing untuk mengatur jarak antar label.
         * Margin dikompensasi agar border-spacing di tepi tabel tidak menggeser posisi:
         *   margin_left_adjusted = margin_left - h_gap
         *   margin_top_adjusted  = margin_top  - v_gap
         */
        .label-table {
            border-collapse: separate;
            border-spacing: {{ $config['h_gap'] }}mm {{ $config['v_gap'] }}mm;
            margin-left: {{ $config['margin_left'] - $config['h_gap'] }}mm;
            margin-top: {{ $config['margin_top'] - $config['v_gap'] }}mm;
            table-layout: fixed;
        }

        /*
         * === SEL LABEL ===
         * Setiap <td> = 1 label pada kertas T&J 108.
         * Dimensi presisi sesuai konfigurasi.
         * overflow: hidden mencegah teks meluber ke label tetangga.
         * word-wrap: break-word memecah kata panjang.
         */
        .label-table td {
            width: {{ $config['label_width'] }}mm;
            height: {{ $config['label_height'] }}mm;
            max-width: {{ $config['label_width'] }}mm;
            max-height: {{ $config['label_height'] }}mm;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            font-size: {{ $config['font_size'] }}pt;
            padding: {{ $config['padding'] }}mm;
            word-wrap: break-word;
            word-break: break-word;
            line-height: 1.2;
            /* Tanpa border untuk cetak final (uncomment untuk debug) */
            /* border: 0.1mm solid #ccc; */
        }

        /*
         * === INFO HALAMAN (opsional) ===
         * Nomor halaman di pojok bawah, sangat kecil agar tidak mengganggu.
         */
        .page-info {
            position: absolute;
            bottom: 2mm;
            right: 4mm;
            font-size: 5pt;
            color: #999;
        }
    </style>
</head>
<body>
    @foreach ($pages as $pageIndex => $pageData)
    <div class="page">
        <table class="label-table">
            @for ($r = 1; $r <= $config['rows']; $r++)
            <tr>
                @for ($c = 1; $c <= $config['cols']; $c++)
                <td>
                    @if (isset($pageData['labels']["$r-$c"]))
                        {{ $pageData['labels']["$r-$c"] }}
                    @endif
                </td>
                @endfor
            </tr>
            @endfor
        </table>

        {{-- Nomor halaman (jika multi-halaman) --}}
        @if ($totalPages > 1)
        <div class="page-info">
            Hal. {{ $pageData['page'] }} / {{ $totalPages }}
        </div>
        @endif
    </div>
    @endforeach
</body>
</html>
