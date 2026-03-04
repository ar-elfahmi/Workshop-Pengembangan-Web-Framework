{{--
    Template PDF Label Tom & Jerry 108 (T&J 108)

    Template ini di-render oleh DomPDF untuk menghasilkan PDF label
    dengan layout grid presisi pada kertas A4.

    Variabel yang diterima:
    - $config: Array konfigurasi dimensi kertas dan label
    - $pages: Array halaman, masing-masing berisi 'labels' (map "baris-kolom" => teks)
    - $totalPages: Total jumlah halaman

    CATATAN PENTING:
    - Semua dimensi menggunakan satuan mm (milimeter)
    - DomPDF mendukung unit mm dalam CSS
    - Jangan tambahkan scaling atau transform apapun
    - @page margin: 0 agar DomPDF tidak menambah margin sendiri
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label T&J 108</title>
    <style>
        /*
         * === PENGATURAN HALAMAN ===
         * Size eksplisit A4, margin 0 agar posisi label presisi.
         * Semua positioning dihitung dari tepi kertas.
         */
        @page {
            size: 210mm 297mm;
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
         * Setiap .page merepresentasikan 1 lembar kertas A4.
         * page-break-after memastikan setiap halaman dimulai di lembar baru.
         */
        .page {
            width: 210mm;
            height: 297mm;
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
         * border-collapse: collapse menghilangkan gap antar sel.
         * Margin mengatur posisi tabel dari tepi kertas.
         */
        .label-table {
            border-collapse: collapse;
            border-spacing: 0;
            margin-left: {{ $config['margin_left'] }}mm;
            margin-top: {{ $config['margin_top'] }}mm;
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
