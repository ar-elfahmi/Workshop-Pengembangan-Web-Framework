{{--
    Halaman Cetak Label T&J 108

    Halaman utama untuk fitur pencetakan label pada kertas Tom & Jerry 108.
    Spesifikasi: 4 kolom x 10 baris = 40 label per lembar (127mm x 205mm)

    Mendukung dua sumber data:
    - Kategori: mencetak nama_kategori sebagai teks label
    - Buku: mencetak judul buku sebagai teks label

    Terdiri dari 3 bagian:
    1. Pilih Item: Tab switcher + Checkbox daftar item dengan input jumlah
    2. Konfigurasi Grid: Visual grid 4x10 untuk menandai posisi terpakai
    3. Aksi: Tombol Preview, Download, dan Kalibrasi

    Variabel dari controller:
    - $type: 'kategori' atau 'buku'
    - $items: Collection of {id, name, sub}
    - $config: Array konfigurasi dimensi label

    Interaksi sepenuhnya client-side menggunakan JavaScript vanilla.
    Data dikirim ke server via form POST saat Preview/Download.
--}}
@extends('layouts.app')

@section('title', 'Cetak Label T&J 108')

@push('page-styles')
<style>
    /*
     * === GRID KOORDINAT ===
     * Visual representation dari kertas T&J 108: 4 kolom x 10 baris = 40 label.
     * Setiap sel bisa diklik untuk toggle status "terpakai".
     */
    .grid-container {
        overflow-x: auto;
        padding: 10px 0;
    }

    .grid-table {
        border-collapse: collapse;
        user-select: none;
        margin: 0 auto;
    }

    .grid-table th {
        font-size: 12px;
        padding: 6px 4px;
        text-align: center;
        color: #6c757d;
        font-weight: 600;
        min-width: 130px;
    }

    .grid-table th.row-header {
        min-width: 44px;
        padding-right: 8px;
    }

    /*
     * Sel grid: representasi 1 label pada kertas.
     * 3 state:
     * - Tersedia kosong (putih)          -> label bisa digunakan, belum ada item
     * - Tersedia terisi (hijau)          -> item sudah di-assign ke posisi ini
     * - Terpakai/disabled (merah)        -> user menandai posisi sudah terpakai
     */
    .grid-cell {
        width: 130px;
        height: 48px;
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        font-size: 11px;
        line-height: 1.2;
        padding: 3px;
        transition: all 0.15s ease;
        position: relative;
        overflow: hidden;
    }

    /* State: tersedia kosong */
    .grid-cell.available {
        background-color: #ffffff;
        color: #adb5bd;
    }

    .grid-cell.available:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }

    /* State: tersedia dan terisi item */
    .grid-cell.assigned {
        background-color: #d4edda;
        border-color: #28a745;
        color: #155724;
        font-weight: 600;
        cursor: default;
    }

    /* State: terpakai (disabled oleh user) */
    .grid-cell.used {
        background-color: #f8d7da;
        border-color: #dc3545;
        color: #721c24;
        cursor: pointer;
    }

    .grid-cell.used::after {
        content: '✕';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        color: rgba(220, 53, 69, 0.4);
        pointer-events: none;
    }

    .grid-cell.used:hover {
        background-color: #f5c6cb;
    }

    /* Label koordinat dalam sel */
    .grid-cell .coord-label {
        font-size: 10px;
        color: #adb5bd;
        display: block;
    }

    .grid-cell.used .coord-label,
    .grid-cell.assigned .coord-label {
        display: none;
    }

    .grid-cell .item-text {
        font-size: 10px;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        max-height: 36px;
    }

    /*
     * === ITEM SELECTION ===
     * Checkbox list dengan input jumlah
     */
    .item-row {
        display: flex;
        align-items: center;
        padding: 6px 10px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.15s ease;
    }

    .item-row:hover {
        background-color: #f8f9fa;
    }

    .item-row.selected {
        background-color: #e8f5e9;
    }

    .item-row label {
        flex: 1;
        margin-bottom: 0;
        cursor: pointer;
        font-size: 14px;
    }

    .item-row label small {
        display: block;
        font-size: 11px;
        color: #888;
        font-weight: normal;
    }

    .qty-input {
        width: 55px;
        text-align: center;
        font-size: 13px;
        padding: 2px 4px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .qty-input:disabled {
        background-color: #f5f5f5;
        color: #aaa;
    }

    /*
     * === STATISTIK ===
     */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .stat-item {
        text-align: center;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .stat-item .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #4B49AC;
    }

    .stat-item .stat-label {
        font-size: 11px;
        color: #6c757d;
    }

    /*
     * === LEGEND ===
     */
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 16px;
        font-size: 12px;
    }

    .legend-box {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 5px;
        border: 1px solid #dee2e6;
    }

    /*
     * === TOOLBAR GRID ===
     */
    .grid-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    /*
     * === TYPE SWITCHER TABS ===
     */
    .type-tabs {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 12px;
    }

    .type-tab {
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
    }

    .type-tab:hover {
        color: #4B49AC;
        text-decoration: none;
    }

    .type-tab.active {
        color: #4B49AC;
        border-bottom-color: #4B49AC;
    }

    /*
     * === NOTIFIKASI ===
     */
    .alert-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="page-header">
        <h3 class="page-title">
            <i class="mdi mdi-label-outline"></i> Cetak Label T&J 108
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cetak Label</li>
            </ol>
        </nav>
    </div>

    {{-- ALERT SESSION --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- ============================================ --}}
        {{-- KOLOM KIRI: PILIH ITEM                       --}}
        {{-- ============================================ --}}
        <div class="col-lg-4 col-md-5">
            <div class="card">
                <div class="card-body">
                    {{-- TAB SWITCHER: Kategori / Buku --}}
                    <div class="type-tabs">
                        <a href="{{ url('/label?type=kategori') }}"
                           class="type-tab {{ $type === 'kategori' ? 'active' : '' }}">
                            <i class="mdi mdi-format-list-bulleted"></i> Kategori
                        </a>
                        <a href="{{ url('/label?type=buku') }}"
                           class="type-tab {{ $type === 'buku' ? 'active' : '' }}">
                            <i class="mdi mdi-book-open-variant"></i> Buku
                        </a>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-checkbox-marked-outline"></i>
                            Pilih {{ $type === 'buku' ? 'Buku' : 'Kategori' }}
                        </h4>
                        <span class="badge badge-primary" id="selectedCount">0 dipilih</span>
                    </div>

                    <p class="text-muted" style="font-size: 12px;">
                        Centang {{ $type === 'buku' ? 'buku' : 'kategori' }} yang ingin dicetak dan tentukan jumlah label.
                    </p>

                    {{-- Tombol Select All / Deselect All --}}
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAll">
                            <i class="mdi mdi-checkbox-multiple-marked"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDeselectAll">
                            <i class="mdi mdi-checkbox-multiple-blank-outline"></i> Hapus Semua
                        </button>
                    </div>

                    {{-- Daftar Item --}}
                    <div style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 6px;">
                        @forelse ($items as $item)
                        <div class="item-row" data-id="{{ $item->id }}">
                            <input type="checkbox"
                                   class="item-checkbox mr-2"
                                   id="item-{{ $item->id }}"
                                   value="{{ $item->id }}"
                                   data-name="{{ $item->name }}">
                            <label for="item-{{ $item->id }}" class="mr-2">
                                {{ $item->name }}
                                @if ($item->sub)
                                    <small>{{ $item->sub }}</small>
                                @endif
                            </label>
                            <input type="number"
                                   class="qty-input"
                                   id="qty-{{ $item->id }}"
                                   value="1"
                                   min="1"
                                   max="999"
                                   disabled
                                   title="Jumlah label">
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-alert-circle-outline" style="font-size: 32px;"></i>
                            @if ($type === 'buku')
                                <p class="mt-2 mb-0">Belum ada buku. <a href="{{ url('/buku') }}">Tambah buku</a></p>
                            @else
                                <p class="mt-2 mb-0">Belum ada kategori. <a href="{{ url('/kategori') }}">Tambah kategori</a></p>
                            @endif
                        </div>
                        @endforelse
                    </div>

                    {{-- STATISTIK --}}
                    <div class="mt-3">
                        <h6 class="text-muted"><i class="mdi mdi-chart-bar"></i> Ringkasan</h6>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value" id="statTotalLabels">0</div>
                                <div class="stat-label">Total Label</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="statAvailable">{{ $config['rows'] * $config['cols'] }}</div>
                                <div class="stat-label">Posisi Tersedia</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="statUsed">0</div>
                                <div class="stat-label">Posisi Terpakai</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="statPages">0</div>
                                <div class="stat-label">Lembar Kertas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- KOLOM KANAN: GRID KOORDINAT + AKSI           --}}
        {{-- ============================================ --}}
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-grid"></i> Konfigurasi Grid Koordinat
                    </h4>

                    <p class="text-muted" style="font-size: 12px;">
                        Klik sel untuk menandai posisi yang <strong>sudah terpakai</strong> pada kertas.
                        Label akan dicetak hanya pada posisi yang tersedia (putih/hijau).
                    </p>

                    {{-- TOOLBAR --}}
                    <div class="grid-toolbar">
                        <div>
                            {{-- Legend --}}
                            <span class="legend-item">
                                <span class="legend-box" style="background: #fff;"></span> Tersedia
                            </span>
                            <span class="legend-item">
                                <span class="legend-box" style="background: #d4edda; border-color: #28a745;"></span> Terisi
                            </span>
                            <span class="legend-item">
                                <span class="legend-box" style="background: #f8d7da; border-color: #dc3545;"></span> Terpakai
                            </span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnMarkAllUsed"
                                    title="Tandai semua posisi sebagai terpakai">
                                <i class="mdi mdi-close-box-multiple"></i> Tandai Semua
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="btnClearAllUsed"
                                    title="Bersihkan semua tanda terpakai">
                                <i class="mdi mdi-checkbox-multiple-blank-outline"></i> Bersihkan Semua
                            </button>
                        </div>
                    </div>

                    {{-- GRID VISUAL --}}
                    <div class="grid-container">
                        <table class="grid-table" id="gridTable">
                            <thead>
                                <tr>
                                    <th class="row-header"></th>
                                    @for ($c = 1; $c <= $config['cols']; $c++)
                                        <th>K{{ $c }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for ($r = 1; $r <= $config['rows']; $r++)
                                <tr>
                                    <th class="row-header">B{{ $r }}</th>
                                    @for ($c = 1; $c <= $config['cols']; $c++)
                                    <td class="grid-cell available"
                                        data-row="{{ $r }}"
                                        data-col="{{ $c }}"
                                        id="cell-{{ $r }}-{{ $c }}"
                                        title="Baris {{ $r }}, Kolom {{ $c }}">
                                        <span class="coord-label">B{{ $r }}-K{{ $c }}</span>
                                        <span class="item-text"></span>
                                    </td>
                                    @endfor
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    {{-- TOMBOL AKSI --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            {{-- Preview PDF (buka inline di tab baru via fetch+Blob) --}}
                            <button type="button" class="btn btn-info mr-2" id="btnPreview">
                                <i class="mdi mdi-eye"></i> Preview PDF
                            </button>

                            {{-- Download PDF --}}
                            <button type="button" class="btn btn-success mr-2" id="btnDownload">
                                <i class="mdi mdi-download"></i> Download PDF
                            </button>
                        </div>

                        <div>
                            {{-- Kalibrasi --}}
                            <a href="{{ route('label.calibration') }}" class="btn btn-outline-secondary btn-sm" target="_blank"
                               title="Cetak halaman kalibrasi untuk verifikasi akurasi posisi">
                                <i class="mdi mdi-ruler"></i> Halaman Kalibrasi
                            </a>
                        </div>
                    </div>

                    {{-- Informasi multi-halaman --}}
                    <div class="alert alert-warning mt-3 d-none" id="alertMultiPage">
                        <i class="mdi mdi-information-outline"></i>
                        <span id="alertMultiPageText"></span>
                    </div>

                    {{-- Informasi tidak cukup item --}}
                    <div class="alert alert-info mt-3 d-none" id="alertNoItems">
                        <i class="mdi mdi-information-outline"></i>
                        Pilih minimal satu item di panel kiri untuk mulai mencetak label.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form tersembunyi untuk submit download ke server --}}
<form id="labelFormDownload" method="POST" action="{{ route('label.download') }}" target="_blank" style="display: none;">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="selected_items" id="formDownloadSelectedItems">
    <input type="hidden" name="used_coords" id="formDownloadUsedCoords">
    <input type="hidden" name="quantities" id="formDownloadQuantities">
</form>
@endsection

@push('page-scripts')
<script>
/**
 * === LABEL T&J 108 - CLIENT-SIDE LOGIC ===
 *
 * State management untuk:
 * - selectedItems: Map<id, {name, qty}> item yang dipilih user
 * - usedCoords: Set<"row-col"> posisi yang ditandai terpakai
 *
 * Alur kerja:
 * 1. User centang item → masuk selectedItems
 * 2. User klik grid → toggle usedCoords
 * 3. JS auto-assign item ke koordinat tersedia
 * 4. Grid di-update visual secara real-time
 * 5. Preview: fetch() + Blob URL (inline PDF di tab baru)
 * 6. Download: form POST (trigger download)
 */
document.addEventListener('DOMContentLoaded', function () {

    // === KONFIGURASI ===
    const CONFIG = {
        rows: {{ $config['rows'] }},
        cols: {{ $config['cols'] }},
        totalPerPage: {{ $config['rows'] * $config['cols'] }}
    };

    const DATA_TYPE = '{{ $type }}';

    // === STATE ===
    const selectedItems = new Map();
    const usedCoords = new Set();

    // === DOM ELEMENTS ===
    const gridTable       = document.getElementById('gridTable');
    const btnPreview      = document.getElementById('btnPreview');
    const btnDownload     = document.getElementById('btnDownload');
    const btnSelectAll    = document.getElementById('btnSelectAll');
    const btnDeselectAll  = document.getElementById('btnDeselectAll');
    const btnMarkAllUsed  = document.getElementById('btnMarkAllUsed');
    const btnClearAllUsed = document.getElementById('btnClearAllUsed');

    // Stat elements
    const statTotalLabels = document.getElementById('statTotalLabels');
    const statAvailable   = document.getElementById('statAvailable');
    const statUsed        = document.getElementById('statUsed');
    const statPages       = document.getElementById('statPages');
    const selectedCount   = document.getElementById('selectedCount');
    const alertMultiPage  = document.getElementById('alertMultiPage');
    const alertMultiPageText = document.getElementById('alertMultiPageText');
    const alertNoItems    = document.getElementById('alertNoItems');

    // ========================================================
    // ITEM SELECTION HANDLERS
    // ========================================================

    document.querySelectorAll('.item-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const id = this.value;
            const name = this.dataset.name;
            const qtyInput = document.getElementById('qty-' + id);
            const itemRow = this.closest('.item-row');

            if (this.checked) {
                selectedItems.set(id, { name: name, qty: parseInt(qtyInput.value) || 1 });
                qtyInput.disabled = false;
                itemRow.classList.add('selected');
            } else {
                selectedItems.delete(id);
                qtyInput.disabled = true;
                qtyInput.value = 1;
                itemRow.classList.remove('selected');
            }
            updateAll();
        });
    });

    document.querySelectorAll('.qty-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const id = this.id.replace('qty-', '');
            if (selectedItems.has(id)) {
                const val = Math.max(1, Math.min(999, parseInt(this.value) || 1));
                this.value = val;
                selectedItems.get(id).qty = val;
                updateAll();
            }
        });
    });

    btnSelectAll.addEventListener('click', function () {
        document.querySelectorAll('.item-checkbox').forEach(function (cb) {
            if (!cb.checked) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    btnDeselectAll.addEventListener('click', function () {
        document.querySelectorAll('.item-checkbox').forEach(function (cb) {
            if (cb.checked) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    // ========================================================
    // GRID KOORDINAT HANDLERS
    // ========================================================

    gridTable.addEventListener('click', function (e) {
        const cell = e.target.closest('.grid-cell');
        if (!cell) return;

        const key = cell.dataset.row + '-' + cell.dataset.col;

        if (usedCoords.has(key)) {
            usedCoords.delete(key);
        } else {
            usedCoords.add(key);
        }
        updateAll();
    });

    btnMarkAllUsed.addEventListener('click', function () {
        for (let r = 1; r <= CONFIG.rows; r++) {
            for (let c = 1; c <= CONFIG.cols; c++) {
                usedCoords.add(r + '-' + c);
            }
        }
        updateAll();
    });

    btnClearAllUsed.addEventListener('click', function () {
        usedCoords.clear();
        updateAll();
    });

    // ========================================================
    // CORE: UPDATE ALL STATE & VISUAL
    // ========================================================

    function updateAll() {
        // 1. Buat daftar label (memperhitungkan kuantitas)
        const labels = [];
        selectedItems.forEach(function (item, id) {
            for (let i = 0; i < item.qty; i++) {
                labels.push({ id: id, name: item.name });
            }
        });

        // 2. Hitung koordinat tersedia
        const available = [];
        for (let r = 1; r <= CONFIG.rows; r++) {
            for (let c = 1; c <= CONFIG.cols; c++) {
                if (!usedCoords.has(r + '-' + c)) {
                    available.push({ row: r, col: c });
                }
            }
        }

        // 3. Mapping label ke koordinat
        const assignmentMap = {};
        let labelIndex = 0;
        for (let i = 0; i < available.length && labelIndex < labels.length; i++) {
            assignmentMap[available[i].row + '-' + available[i].col] = labels[labelIndex].name;
            labelIndex++;
        }

        // 4. Update visual grid
        for (let r = 1; r <= CONFIG.rows; r++) {
            for (let c = 1; c <= CONFIG.cols; c++) {
                const key = r + '-' + c;
                const cell = document.getElementById('cell-' + key);
                const itemText = cell.querySelector('.item-text');

                cell.className = 'grid-cell';

                if (usedCoords.has(key)) {
                    cell.classList.add('used');
                    itemText.textContent = '';
                } else if (assignmentMap[key]) {
                    cell.classList.add('assigned');
                    itemText.textContent = assignmentMap[key];
                } else {
                    cell.classList.add('available');
                    itemText.textContent = '';
                }
            }
        }

        // 5. Hitung statistik
        const totalLabels = labels.length;
        const totalUsed = usedCoords.size;
        const totalAvailable = CONFIG.totalPerPage - totalUsed;
        let totalPages = 0;

        if (totalLabels > 0) {
            if (totalLabels <= totalAvailable) {
                totalPages = 1;
            } else {
                const remaining = totalLabels - totalAvailable;
                totalPages = 1 + Math.ceil(remaining / CONFIG.totalPerPage);
            }
        }

        statTotalLabels.textContent = totalLabels;
        statAvailable.textContent = totalAvailable;
        statUsed.textContent = totalUsed;
        statPages.textContent = totalPages;
        selectedCount.textContent = selectedItems.size + ' dipilih';

        if (totalPages > 1) {
            alertMultiPage.classList.remove('d-none');
            alertMultiPageText.textContent =
                'Item Anda membutuhkan ' + totalPages + ' lembar kertas. ' +
                'Halaman 2 dan seterusnya akan menggunakan semua ' +
                CONFIG.totalPerPage + ' posisi.';
        } else {
            alertMultiPage.classList.add('d-none');
        }

        alertNoItems.classList.toggle('d-none', totalLabels > 0);
    }

    // ========================================================
    // PDF ACTIONS
    // ========================================================

    /**
     * Mengumpulkan data form yang diperlukan untuk generate PDF
     */
    function collectFormData() {
        if (selectedItems.size === 0) {
            showNotification('Pilih minimal satu item untuk dicetak.', 'warning');
            return null;
        }

        const ids = [];
        const quantities = {};
        selectedItems.forEach(function (item, id) {
            ids.push(parseInt(id));
            quantities[id] = item.qty;
        });

        const coordsArray = [];
        usedCoords.forEach(function (key) {
            const parts = key.split('-');
            coordsArray.push([parseInt(parts[0]), parseInt(parts[1])]);
        });

        return {
            type: DATA_TYPE,
            selected_items: JSON.stringify(ids),
            used_coords: JSON.stringify(coordsArray),
            quantities: JSON.stringify(quantities)
        };
    }

    /**
     * Preview PDF menggunakan fetch() + Blob URL.
     * Ini memastikan PDF ditampilkan inline di tab baru, BUKAN di-download.
     * Berbeda dari form submit yang bergantung pada Content-Disposition header
     * yang kadang diabaikan browser.
     */
    btnPreview.addEventListener('click', function () {
        const data = collectFormData();
        if (!data) return;

        // Tampilkan loading state
        btnPreview.disabled = true;
        btnPreview.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Generating...';

        // Buat FormData
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('type', data.type);
        formData.append('selected_items', data.selected_items);
        formData.append('used_coords', data.used_coords);
        formData.append('quantities', data.quantities);

        // Fetch PDF sebagai blob, lalu buka sebagai Blob URL di tab baru
        fetch('{{ route("label.preview") }}', {
            method: 'POST',
            body: formData
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Gagal generate PDF. Status: ' + response.status);
            return response.blob();
        })
        .then(function (blob) {
            var pdfBlob = new Blob([blob], { type: 'application/pdf' });
            var url = URL.createObjectURL(pdfBlob);
            window.open(url, '_blank');
            // Bersihkan Blob URL setelah 1 menit
            setTimeout(function () { URL.revokeObjectURL(url); }, 60000);
        })
        .catch(function (err) {
            showNotification('Gagal generate preview: ' + err.message, 'danger');
        })
        .finally(function () {
            btnPreview.disabled = false;
            btnPreview.innerHTML = '<i class="mdi mdi-eye"></i> Preview PDF';
        });
    });

    /**
     * Download PDF via hidden form submit
     */
    btnDownload.addEventListener('click', function () {
        const data = collectFormData();
        if (!data) return;

        document.getElementById('formDownloadSelectedItems').value = data.selected_items;
        document.getElementById('formDownloadUsedCoords').value = data.used_coords;
        document.getElementById('formDownloadQuantities').value = data.quantities;

        document.getElementById('labelFormDownload').submit();
    });

    // ========================================================
    // UTILITAS
    // ========================================================

    function showNotification(message, type) {
        type = type || 'info';
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-floating alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML =
            '<i class="mdi mdi-' + (type === 'warning' ? 'alert' : 'information') + '-outline"></i> ' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button>';

        document.body.appendChild(alertDiv);

        setTimeout(function () {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('show');
                setTimeout(function () { alertDiv.remove(); }, 300);
            }
        }, 4000);
    }

    // Inisialisasi
    updateAll();
});
</script>
@endpush
