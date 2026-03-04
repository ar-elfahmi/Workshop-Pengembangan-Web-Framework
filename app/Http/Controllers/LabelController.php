<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * LabelController
 *
 * Menangani fitur pencetakan label PDF pada kertas Tom & Jerry 108 (T&J 108).
 * Kertas T&J 108 memiliki layout grid 12 kolom x 9 baris = 108 label per lembar
 * pada kertas ukuran A4 (210mm x 297mm).
 *
 * Fitur utama:
 * - Pilih item (kategori) yang ingin dicetak
 * - Konfigurasi grid koordinat (tandai posisi yang sudah terpakai)
 * - Preview PDF sebelum cetak
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
     * VERIFIKASI DIMENSI:
     *   Total lebar  = margin_left + (cols * label_width) + ((cols-1) * h_gap) + margin_right = 210mm
     *   Total tinggi = margin_top + (rows * label_height) + ((rows-1) * v_gap) + margin_bottom = 297mm
     *
     * CATATAN: Sesuaikan nilai-nilai ini dengan kertas T&J 108 yang sebenarnya.
     * Gunakan halaman kalibrasi (/label/kalibrasi) untuk verifikasi dan fine-tuning.
     *
     * @return array Konfigurasi dimensi label
     */
    private function getLabelConfig(): array
    {
        return [
            // === Dimensi Kertas (A4) ===
            'paper_width'   => 210,     // mm - Lebar kertas A4
            'paper_height'  => 297,     // mm - Tinggi kertas A4

            // === Margin Kertas ===
            'margin_top'    => 13.5,    // mm - Margin atas
            'margin_left'   => 9,       // mm - Margin kiri
            'margin_bottom' => 13.5,    // mm - Margin bawah (kalkulasi: 297 - 13.5 - 9*30 = 13.5)
            'margin_right'  => 9,       // mm - Margin kanan (kalkulasi: 210 - 9 - 12*16 = 9)

            // === Dimensi Label ===
            'label_width'   => 16,      // mm - Lebar setiap label
            'label_height'  => 30,      // mm - Tinggi setiap label

            // === Layout Grid ===
            'cols'          => 12,      // Jumlah kolom per baris
            'rows'          => 9,       // Jumlah baris per halaman

            // === Jarak Antar Label ===
            'h_gap'         => 0,       // mm - Jarak horizontal antar label (T&J 108 = 0)
            'v_gap'         => 0,       // mm - Jarak vertikal antar label (T&J 108 = 0)

            // === Pengaturan Teks ===
            'font_size'     => 7,       // pt - Ukuran font default untuk label
            'padding'       => 1,       // mm - Padding dalam label (safety margin dari tepi)
        ];
    }

    /**
     * Halaman utama pencetakan label.
     * Menampilkan daftar kategori dengan checkbox dan grid koordinat visual.
     */
    public function index()
    {
        $kategori = Kategori::all();
        $config = $this->getLabelConfig();

        return view('pages.label.index', compact('kategori', 'config'));
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
     * Mencetak grid penuh 12x9 dengan label koordinat (B1-K1, B1-K2, dst)
     * untuk verifikasi akurasi posisi cetak pada kertas T&J 108 sebenarnya.
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

        $pdf = Pdf::loadView('pages.label.pdf', compact('config', 'pages', 'totalPages'))
            ->setPaper('a4', 'portrait');

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
        ]);

        $config = $this->getLabelConfig();

        // Decode data JSON dari form
        $selectedIds = json_decode($request->input('selected_items'), true);
        $usedCoords = json_decode($request->input('used_coords', '[]'), true);
        $quantities = json_decode($request->input('quantities', '{}'), true);

        if (empty($selectedIds)) {
            return back()->with('error', 'Pilih minimal satu item untuk dicetak.');
        }

        // Ambil data kategori yang dipilih (urut sesuai urutan pilihan)
        $kategoriMap = Kategori::whereIn('idkategori', $selectedIds)
            ->get()
            ->keyBy('idkategori');

        if ($kategoriMap->isEmpty()) {
            return back()->with('error', 'Item yang dipilih tidak ditemukan dalam database.');
        }

        // Buat daftar label yang akan dicetak (dengan kuantitas)
        // Setiap entri = satu label yang perlu dicetak
        $labelsToPrint = [];
        foreach ($selectedIds as $id) {
            if (!isset($kategoriMap[$id])) continue;
            $qty = isset($quantities[$id]) ? max(1, (int)$quantities[$id]) : 1;
            for ($i = 0; $i < $qty; $i++) {
                $labelsToPrint[] = $kategoriMap[$id]->nama_kategori;
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
        // Urutan: (1,1) -> (1,2) -> ... -> (1,12) -> (2,1) -> ... -> (9,12)
        $availableCoords = [];
        for ($r = 1; $r <= $config['rows']; $r++) {
            for ($c = 1; $c <= $config['cols']; $c++) {
                if (!isset($usedSet["$r-$c"])) {
                    $availableCoords[] = [$r, $c];
                }
            }
        }

        $totalItems = count($labelsToPrint);
        $totalPerPage = $config['rows'] * $config['cols']; // 108
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

        // === Halaman 2+: semua 108 koordinat tersedia ===
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

        // Generate PDF
        $pdf = Pdf::loadView('pages.label.pdf', compact('config', 'pages', 'totalPages'))
            ->setPaper('a4', 'portrait');

        $filename = 'label-tj108-' . date('Y-m-d-His') . '.pdf';

        if ($output === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
