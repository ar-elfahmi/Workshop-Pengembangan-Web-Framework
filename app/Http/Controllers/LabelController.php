<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * LabelController
 *
 * Menangani fitur pencetakan label PDF pada kertas Tom & Jerry 108 (T&J 108).
 * Mendukung dua sumber data: Kategori dan Buku.
 *
 * Kertas T&J 108 memiliki layout grid 4 kolom x 10 baris = 40 label per lembar
 * pada kertas ukuran kustom ±127mm x 205mm (sedikit lebih kecil dari A5).
 *
 * Spesifikasi Kertas T&J No. 108:
 * - Ukuran Label (kemasan): 18mm x 38mm
 * - Ukuran Lembar: ±127mm x 205mm
 * - Layout: 4 Kolom x 10 Baris = 40 stiker/lembar
 * - Margin Atas/Bawah: ±12mm, Kiri/Kanan: ±6mm
 * - Jarak Horizontal: 2mm, Vertikal: 1mm
 * - Material: Kertas HVS putih Self-Adhesive Sticker (Doff)
 *
 * Fitur utama:
 * - Pilih item (kategori ATAU buku) yang ingin dicetak
 * - Konfigurasi grid koordinat (tandai posisi yang sudah terpakai)
 * - Preview PDF inline di browser (tanpa download)
 * - Download PDF siap cetak
 * - Halaman kalibrasi untuk verifikasi akurasi posisi
 */
class LabelController extends Controller
{
    /**
     * Konfigurasi dimensi kertas Tom & Jerry 108 (T&J 108)
     *
     * Semua satuan dalam milimeter (mm) kecuali font_size dalam point (pt).
     *
     * RUMUS POSISI LABEL:
     *   X = margin_left + (kolom - 1) * (label_width + h_gap)
     *   Y = margin_top  + (baris - 1) * (label_height + v_gap)
     *
     * PERHITUNGAN DIMENSI CELL LABEL:
     *   label_width  = (paper_width  - margin_left - margin_right  - (cols-1)*h_gap) / cols
     *                = (127 - 6 - 6 - 3*2) / 4 = 27.25mm
     *   label_height = (paper_height - margin_top  - margin_bottom - (rows-1)*v_gap) / rows
     *                = (205 - 12 - 12 - 9*1) / 10 = 17.2mm
     *
     * VERIFIKASI DIMENSI:
     *   Total lebar  = margin_left + (cols * label_width) + ((cols-1) * h_gap) + margin_right = 127mm
     *   Total tinggi = margin_top + (rows * label_height) + ((rows-1) * v_gap) + margin_bottom = 205mm
     *
     * CATATAN:
     * - Stiker T&J 108 berukuran nominal 18mm x 38mm (pada kemasan).
     * - Dimensi cell di bawah dihitung dari layout aktual kertas, margin, dan gap.
     * - Gunakan halaman kalibrasi (/label/kalibrasi) untuk verifikasi dan fine-tuning.
     * - Ukuran kertas di printer: Custom Size 12.7cm x 20.5cm
     *
     * @return array Konfigurasi dimensi label
     */
    private function getLabelConfig(): array
    {
        return [
            // === Dimensi Kertas T&J 108 (Custom Size) ===
            // Sedikit lebih kecil dari A5 (±127mm x 205mm)
            // Pengaturan printer: Custom Size 12.7cm x 20.5cm
            'paper_width'   => 127,     // mm - Lebar kertas T&J 108
            'paper_height'  => 205,     // mm - Tinggi kertas T&J 108

            // === Margin Kertas ===
            'margin_top'    => 12,      // mm - Margin atas (±12mm)
            'margin_left'   => 6,       // mm - Margin kiri (±6mm)
            'margin_bottom' => 12,      // mm - Margin bawah (±12mm)
            'margin_right'  => 6,       // mm - Margin kanan (±6mm)

            // === Dimensi Cell Label ===
            // Dihitung: (paper - margins - gaps) / jumlah
            // Stiker T&J 108 pada kemasan: 18mm x 38mm
            'label_width'   => 27.25,   // mm - Lebar cell = (127-6-6-3*2)/4
            'label_height'  => 17.2,    // mm - Tinggi cell = (205-12-12-9*1)/10

            // === Layout Grid ===
            'cols'          => 4,       // 4 kolom per baris (Horizontal)
            'rows'          => 10,      // 10 baris per halaman (Vertikal)
            // Total: 4 x 10 = 40 stiker label per lembar
            // Total per pak: 10 lembar x 40 = 400 stiker

            // === Jarak Antar Label ===
            'h_gap'         => 2,       // mm - Jarak horizontal antar kolom
            'v_gap'         => 1,       // mm - Jarak vertikal antar baris

            // === Pengaturan Teks ===
            'font_size'     => 8,       // pt - Ukuran font (8pt optimal untuk cell 27x17mm)
            'padding'       => 1,       // mm - Padding dalam label (safety margin dari tepi)
        ];
    }

    /**
     * Halaman utama pencetakan label.
     * Mendukung dua tipe data: 'kategori' (default) dan 'buku'.
     * Tipe ditentukan via query parameter ?type=kategori|buku
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'kategori');
        if (!in_array($type, ['kategori', 'buku'])) {
            $type = 'kategori';
        }

        $config = $this->getLabelConfig();

        // Load data sesuai tipe
        if ($type === 'buku') {
            $items = Buku::with('kategori')->get()->map(function ($b) {
                return (object) [
                    'id'   => $b->idbuku,
                    'name' => $b->judul,
                    'sub'  => $b->kode . ' — ' . ($b->kategori->nama_kategori ?? ''),
                ];
            });
        } else {
            $items = Kategori::all()->map(function ($k) {
                return (object) [
                    'id'   => $k->idkategori,
                    'name' => $k->nama_kategori,
                    'sub'  => '',
                ];
            });
        }

        return view('pages.label.index', compact('items', 'config', 'type'));
    }

    /**
     * Generate preview PDF (tampil inline di browser).
     * Menerima data item terpilih dan koordinat terpakai via POST.
     */
    public function preview(Request $request)
    {
        return $this->generatePdf($request, 'stream');
    }

    /**
     * Download PDF label.
     * Menerima data item terpilih dan koordinat terpakai via POST.
     */
    public function download(Request $request)
    {
        return $this->generatePdf($request, 'download');
    }

    /**
     * Generate halaman kalibrasi.
     * Mencetak grid penuh 4x10 dengan label koordinat (B1-K1, B1-K2, dst)
     * untuk verifikasi akurasi posisi cetak pada kertas T&J 108 sebenarnya.
     *
     * Instruksi:
     * 1. Cetak halaman ini pada kertas T&J 108
     * 2. Gunakan Custom Size 12.7cm x 20.5cm di pengaturan printer
     * 3. Pastikan scaling = 100% (tanpa fit-to-page)
     * 4. Verifikasi setiap label koordinat sejajar dengan posisi stiker
     * 5. Jika meleset, sesuaikan margin di getLabelConfig()
     */
    public function calibration()
    {
        $config = $this->getLabelConfig();
        $labelMap = [];

        // Isi semua koordinat dengan label posisi
        for ($r = 1; $r <= $config['rows']; $r++) {
            for ($c = 1; $c <= $config['cols']; $c++) {
                $labelMap["$r-$c"] = "B$r-K$c";
            }
        }

        $pages = [['labels' => $labelMap, 'page' => 1]];
        $totalPages = 1;

        // Konversi mm ke pt untuk custom paper size (1mm = 72/25.4 pt)
        $paperWidthPt  = $config['paper_width'] * 72 / 25.4;   // 127mm = 360pt
        $paperHeightPt = $config['paper_height'] * 72 / 25.4;  // 205mm = 581.1pt

        $pdf = Pdf::loadView('pages.label.pdf', compact('config', 'pages', 'totalPages'))
            ->setPaper([0, 0, $paperWidthPt, $paperHeightPt], 'portrait');

        return $pdf->stream('kalibrasi-tj108.pdf');
    }

    /**
     * Logic utama untuk generate PDF label.
     *
     * Alur kerja:
     * 1. Validasi input (item terpilih)
     * 2. Parse koordinat yang sudah terpakai
     * 3. Hitung koordinat tersedia secara berurutan
     * 4. Mapping item ke koordinat (dengan dukungan jumlah/kuantitas)
     * 5. Jika item > koordinat tersedia halaman 1, lanjut ke halaman berikutnya
     * 6. Generate PDF dengan DomPDF
     *
     * @param Request $request Data dari form (selected_items, used_coords, quantities)
     * @param string  $output  'stream' untuk preview inline, 'download' untuk unduh
     * @return \Illuminate\Http\Response
     */
    private function generatePdf(Request $request, string $output)
    {
        $request->validate([
            'selected_items' => 'required|string',
            'type'           => 'required|in:kategori,buku',
        ]);

        $config = $this->getLabelConfig();
        $type = $request->input('type', 'kategori');

        // Decode data JSON dari form
        $selectedIds = json_decode($request->input('selected_items'), true);
        $usedCoords = json_decode($request->input('used_coords', '[]'), true);
        $quantities = json_decode($request->input('quantities', '{}'), true);

        if (empty($selectedIds)) {
            return back()->with('error', 'Pilih minimal satu item untuk dicetak.');
        }

        // Ambil data sesuai tipe dan buat mapping id => label text
        if ($type === 'buku') {
            $dataMap = Buku::whereIn('idbuku', $selectedIds)
                ->get()
                ->keyBy('idbuku')
                ->map(fn($b) => $b->judul);
            $pkField = 'idbuku';
        } else {
            $dataMap = Kategori::whereIn('idkategori', $selectedIds)
                ->get()
                ->keyBy('idkategori')
                ->map(fn($k) => $k->nama_kategori);
            $pkField = 'idkategori';
        }

        if ($dataMap->isEmpty()) {
            return back()->with('error', 'Item yang dipilih tidak ditemukan dalam database.');
        }

        // Buat daftar label yang akan dicetak (dengan kuantitas)
        // Setiap entri = satu label yang perlu dicetak
        $labelsToPrint = [];
        foreach ($selectedIds as $id) {
            if (!isset($dataMap[$id])) continue;
            $qty = isset($quantities[$id]) ? max(1, (int)$quantities[$id]) : 1;
            for ($i = 0; $i < $qty; $i++) {
                $labelsToPrint[] = $dataMap[$id];
            }
        }

        if (empty($labelsToPrint)) {
            return back()->with('error', 'Tidak ada label yang bisa dicetak.');
        }

        // Buat set koordinat terpakai untuk lookup O(1)
        $usedSet = [];
        foreach ($usedCoords as $coord) {
            if (is_array($coord) && count($coord) >= 2) {
                $usedSet["{$coord[0]}-{$coord[1]}"] = true;
            }
        }

        // Hitung koordinat tersedia pada halaman pertama
        // Urutan: (1,1) -> (1,2) -> ... -> (1,4) -> (2,1) -> ... -> (10,4)
        $availableCoords = [];
        for ($r = 1; $r <= $config['rows']; $r++) {
            for ($c = 1; $c <= $config['cols']; $c++) {
                if (!isset($usedSet["$r-$c"])) {
                    $availableCoords[] = [$r, $c];
                }
            }
        }

        $totalItems = count($labelsToPrint);
        $totalPerPage = $config['rows'] * $config['cols']; // 40 label per lembar
        $pages = [];
        $itemIndex = 0;
        $pageNum = 1;

        // === Halaman 1: gunakan koordinat yang tersedia ===
        $pageLabels = [];
        foreach ($availableCoords as $coord) {
            if ($itemIndex >= $totalItems) break;
            $key = "{$coord[0]}-{$coord[1]}";
            $pageLabels[$key] = $labelsToPrint[$itemIndex];
            $itemIndex++;
        }
        $pages[] = ['labels' => $pageLabels, 'page' => $pageNum];

        // === Halaman 2+: semua 40 koordinat tersedia (lembar baru) ===
        while ($itemIndex < $totalItems) {
            $pageNum++;
            $pageLabels = [];
            for ($r = 1; $r <= $config['rows']; $r++) {
                for ($c = 1; $c <= $config['cols']; $c++) {
                    if ($itemIndex >= $totalItems) break 2;
                    $key = "$r-$c";
                    $pageLabels[$key] = $labelsToPrint[$itemIndex];
                    $itemIndex++;
                }
            }
            $pages[] = ['labels' => $pageLabels, 'page' => $pageNum];
        }

        $totalPages = count($pages);

        // Generate PDF dengan ukuran kertas kustom T&J 108
        // Konversi mm ke pt: 1mm = 72/25.4 pt
        $paperWidthPt  = $config['paper_width'] * 72 / 25.4;   // 127mm = 360pt
        $paperHeightPt = $config['paper_height'] * 72 / 25.4;  // 205mm = 581.1pt

        $pdf = Pdf::loadView('pages.label.pdf', compact('config', 'pages', 'totalPages'))
            ->setPaper([0, 0, $paperWidthPt, $paperHeightPt], 'portrait');

        $filename = 'label-tj108-' . date('Y-m-d-His') . '.pdf';

        if ($output === 'stream') {
            // Preview inline: return response dengan Content-Disposition: inline
            // agar browser menampilkan PDF di tab baru (bukan download)
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        return $pdf->download($filename);
    }
}
