<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LabelPrintController extends Controller
{
    private const DEFAULT_CONFIG = [
        'paper_width_mm' => 127,
        'paper_height_mm' => 205,
        'rows' => 12,
        'cols' => 9,
        'margin_top_mm' => 12,
        'margin_right_mm' => 6,
        'margin_bottom_mm' => 12,
        'margin_left_mm' => 6,
        'gutter_x_mm' => 1,
        'gutter_y_mm' => 1,
        'font_size_pt' => 7,
    ];

    public function index()
    {
        $books = Buku::with('kategori:idkategori,nama_kategori')
            ->select(['idbuku', 'idkategori', 'kode', 'judul', 'pengarang'])
            ->orderBy('judul')
            ->get();

        $bookPayload = $books->map(static function ($book) {
            return [
                'id' => $book->idbuku,
                'kategori' => $book->kategori->nama_kategori ?? '-',
                'kode' => $book->kode,
                'judul' => $book->judul,
                'pengarang' => $book->pengarang,
            ];
        })->values();

        return view('pages.buku.labels', [
            'books' => $books,
            'bookPayload' => $bookPayload,
            'defaultConfig' => self::DEFAULT_CONFIG,
        ]);
    }

    public function generatePdf(Request $request)
    {
        $validated = $request->validate([
            'selected_books' => ['required', 'array', 'min:1'],
            'selected_books.*' => ['integer'],
            'occupied_cells' => ['nullable', 'string'],
            'paper_width_mm' => ['required', 'numeric', 'min:90', 'max:215'],
            'paper_height_mm' => ['required', 'numeric', 'min:120', 'max:330'],
            'rows' => ['required', 'integer', 'min:1', 'max:50'],
            'cols' => ['required', 'integer', 'min:1', 'max:50'],
            'margin_top_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_right_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_bottom_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_left_mm' => ['required', 'numeric', 'min:0', 'max:50'],
            'gutter_x_mm' => ['required', 'numeric', 'min:0', 'max:10'],
            'gutter_y_mm' => ['required', 'numeric', 'min:0', 'max:10'],
            'font_size_pt' => ['required', 'numeric', 'min:6', 'max:14'],
        ]);

        $config = $this->buildConfig($validated);
        $this->assertGridDimensions($config);

        $selectedIds = collect($validated['selected_books'])
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values();

        $books = Buku::with('kategori:idkategori,nama_kategori')
            ->whereIn('idbuku', $selectedIds)
            ->select(['idbuku', 'idkategori', 'kode', 'judul', 'pengarang'])
            ->get()
            ->keyBy('idbuku');

        $orderedBooks = $selectedIds
            ->map(static fn ($id) => $books->get($id))
            ->filter()
            ->values();

        $occupiedCells = $this->parseOccupiedCells(
            $validated['occupied_cells'] ?? '',
            $config['rows'] * $config['cols']
        );

        [$placements, $overflowItems] = $this->buildPlacements($orderedBooks, $occupiedCells, $config);

        if ($placements->isEmpty()) {
            return back()->withErrors([
                'selected_books' => 'Semua slot label sudah terpakai. Ubah grid atau kosongkan beberapa slot terlebih dahulu.',
            ])->withInput();
        }

        $paperSize = [0, 0, $this->mmToPoints($config['paper_width_mm']), $this->mmToPoints($config['paper_height_mm'])];

        $pdf = Pdf::loadView('pages.buku.labels_pdf', [
            'placements' => $placements,
            'config' => $config,
            'totalSelected' => $orderedBooks->count(),
            'overflowCount' => $overflowItems->count(),
        ])->setPaper($paperSize, 'portrait');

        return $pdf->download('label-tnj108-' . date('Y-m-d-His') . '.pdf');
    }

    private function buildConfig(array $validated): array
    {
        return [
            'paper_width_mm' => (float) $validated['paper_width_mm'],
            'paper_height_mm' => (float) $validated['paper_height_mm'],
            'rows' => (int) $validated['rows'],
            'cols' => (int) $validated['cols'],
            'margin_top_mm' => (float) $validated['margin_top_mm'],
            'margin_right_mm' => (float) $validated['margin_right_mm'],
            'margin_bottom_mm' => (float) $validated['margin_bottom_mm'],
            'margin_left_mm' => (float) $validated['margin_left_mm'],
            'gutter_x_mm' => (float) $validated['gutter_x_mm'],
            'gutter_y_mm' => (float) $validated['gutter_y_mm'],
            'font_size_pt' => (float) $validated['font_size_pt'],
        ];
    }

    private function assertGridDimensions(array &$config): void
    {
        $usableWidth = $config['paper_width_mm'] - $config['margin_left_mm'] - $config['margin_right_mm'] - (($config['cols'] - 1) * $config['gutter_x_mm']);
        $usableHeight = $config['paper_height_mm'] - $config['margin_top_mm'] - $config['margin_bottom_mm'] - (($config['rows'] - 1) * $config['gutter_y_mm']);

        if ($usableWidth <= 0 || $usableHeight <= 0) {
            abort(422, 'Konfigurasi margin/gutter terlalu besar untuk ukuran kertas.');
        }

        $config['cell_width_mm'] = $usableWidth / $config['cols'];
        $config['cell_height_mm'] = $usableHeight / $config['rows'];
    }

    private function parseOccupiedCells(string $occupiedCellsRaw, int $maxCell): array
    {
        if (trim($occupiedCellsRaw) === '') {
            return [];
        }

        return collect(explode(',', $occupiedCellsRaw))
            ->map(static fn ($value) => (int) trim($value))
            ->filter(static fn ($cell) => $cell >= 1 && $cell <= $maxCell)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function buildPlacements($items, array $occupiedCells, array $config): array
    {
        $totalCells = $config['rows'] * $config['cols'];
        $occupiedMap = array_fill(1, $totalCells, false);

        foreach ($occupiedCells as $cell) {
            $occupiedMap[$cell] = true;
        }

        $placements = collect();
        $currentIndex = 0;

        foreach ($items as $item) {
            while ($currentIndex < $totalCells && $occupiedMap[$currentIndex + 1]) {
                $currentIndex++;
            }

            if ($currentIndex >= $totalCells) {
                break;
            }

            $cellNumber = $currentIndex + 1;
            $row = intdiv($currentIndex, $config['cols']);
            $col = $currentIndex % $config['cols'];

            $x = $config['margin_left_mm'] + ($col * ($config['cell_width_mm'] + $config['gutter_x_mm']));
            $y = $config['margin_top_mm'] + ($row * ($config['cell_height_mm'] + $config['gutter_y_mm']));

            $placements->push([
                'cell_number' => $cellNumber,
                'x' => round($x, 4),
                'y' => round($y, 4),
                'width' => round($config['cell_width_mm'], 4),
                'height' => round($config['cell_height_mm'], 4),
                'book' => $item,
            ]);

            $occupiedMap[$cellNumber] = true;
            $currentIndex++;
        }

        return [$placements, $items->slice($placements->count())->values()];
    }

    private function mmToPoints(float $mm): float
    {
        return $mm * 2.8346456693;
    }
}
