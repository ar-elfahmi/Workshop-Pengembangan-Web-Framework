@extends('layouts.app')

@section('title', 'Cetak Label TnJ 108')

@push('page-styles')
<style>
  .label-grid-board {
    display: grid;
    gap: 6px;
    max-height: 320px;
    overflow: auto;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fafafa;
  }

  .label-grid-cell {
    border: 1px solid #d3d3d3;
    background: #fff;
    color: #777;
    font-size: 11px;
    line-height: 1;
    min-height: 26px;
    border-radius: 4px;
    cursor: pointer;
  }

  .label-grid-cell.is-occupied {
    background: #ffe5e5;
    border-color: #f5a2a2;
    color: #9d2e2e;
    font-weight: 600;
  }

  .preview-shell {
    width: 100%;
    overflow: auto;
    border: 1px dashed #bbb;
    border-radius: 8px;
    background: linear-gradient(180deg, #fafafa 0%, #f3f3f3 100%);
    padding: 16px;
  }

  .preview-paper {
    position: relative;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
  }

  .preview-label {
    position: absolute;
    border: 1px solid #d7d7d7;
    border-radius: 2px;
    padding: 3px;
    overflow: hidden;
    font-size: 9px;
    line-height: 1.1;
    background: #fff;
  }

  .preview-label .line-1 {
    font-weight: 700;
    margin-bottom: 2px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }

  .preview-label .line-2,
  .preview-label .line-3 {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }
</style>
@endpush

@section('content')
<div class="container">
  <div class="page-header">
    <h3 class="page-title">Cetak Label TnJ 108</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Buku</a></li>
        <li class="breadcrumb-item active" aria-current="page">Label PDF</li>
      </ol>
    </nav>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Input belum valid:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('buku.labels.pdf') }}" target="_blank" id="labelForm">
    @csrf

    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 class="card-title mb-0">1) Pilih Item Buku</h4>
              <button type="button" class="btn btn-outline-primary btn-sm" id="toggleAllBtn">Pilih Semua</button>
            </div>

            <div class="table-responsive" style="max-height: 380px; overflow: auto;">
              <table class="table table-bordered table-sm mb-0">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Kategori</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($books as $book)
                    <tr>
                      <td class="text-center">
                        <input type="checkbox" name="selected_books[]" value="{{ $book->idbuku }}" class="book-checkbox" {{ in_array($book->idbuku, old('selected_books', [])) ? 'checked' : '' }}>
                      </td>
                      <td>{{ $book->kategori->nama_kategori ?? '-' }}</td>
                      <td>{{ $book->kode }}</td>
                      <td>{{ $book->judul }}</td>
                      <td>{{ $book->pengarang }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted">Belum ada data buku.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card mb-3">
          <div class="card-body">
            <h4 class="card-title">2) Konfigurasi Kertas & Grid</h4>

            <div class="row">
              <div class="col-6 form-group">
                <label>Lebar Kertas (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="paper_width_mm" value="{{ old('paper_width_mm', $defaultConfig['paper_width_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Tinggi Kertas (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="paper_height_mm" value="{{ old('paper_height_mm', $defaultConfig['paper_height_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Rows</label>
                <input type="number" class="form-control config-input" name="rows" value="{{ old('rows', $defaultConfig['rows']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Cols</label>
                <input type="number" class="form-control config-input" name="cols" value="{{ old('cols', $defaultConfig['cols']) }}" required>
              </div>

              <div class="col-6 form-group">
                <label>Margin Atas (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="margin_top_mm" value="{{ old('margin_top_mm', $defaultConfig['margin_top_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Margin Bawah (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="margin_bottom_mm" value="{{ old('margin_bottom_mm', $defaultConfig['margin_bottom_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Margin Kiri (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="margin_left_mm" value="{{ old('margin_left_mm', $defaultConfig['margin_left_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Margin Kanan (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="margin_right_mm" value="{{ old('margin_right_mm', $defaultConfig['margin_right_mm']) }}" required>
              </div>

              <div class="col-6 form-group">
                <label>Gutter X (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="gutter_x_mm" value="{{ old('gutter_x_mm', $defaultConfig['gutter_x_mm']) }}" required>
              </div>
              <div class="col-6 form-group">
                <label>Gutter Y (mm)</label>
                <input type="number" step="0.1" class="form-control config-input" name="gutter_y_mm" value="{{ old('gutter_y_mm', $defaultConfig['gutter_y_mm']) }}" required>
              </div>

              <div class="col-12 form-group mb-0">
                <label>Font Size Label (pt)</label>
                <input type="number" step="0.1" class="form-control config-input" name="font_size_pt" value="{{ old('font_size_pt', $defaultConfig['font_size_pt']) }}" required>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 class="card-title mb-0">3) Tandai Slot Terpakai</h4>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="clearOccupiedBtn">Reset</button>
            </div>
            <p class="text-muted mb-2">Klik kotak untuk menandai sel yang sudah terpakai pada kertas fisik.</p>

            <div id="gridCells" class="label-grid-board"></div>
            <input type="hidden" name="occupied_cells" id="occupiedCellsInput" value="{{ old('occupied_cells', '') }}">
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h4 class="card-title mb-0">4) Preview Layout</h4>
          <button type="submit" class="btn btn-danger">
            <i class="mdi mdi-printer"></i> Generate PDF Siap Cetak
          </button>
        </div>

        <div class="mb-2 text-muted" id="previewSummary"></div>
        <div class="preview-shell">
          <div id="previewPaper" class="preview-paper"></div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('page-scripts')
<script type="application/json" id="booksPayload">@json($bookPayload)</script>
<script>
  (function () {
    const payloadElement = document.getElementById('booksPayload');
    const books = payloadElement ? JSON.parse(payloadElement.textContent || '[]') : [];

    const byId = new Map(books.map((item) => [String(item.id), item]));

    const form = document.getElementById('labelForm');
    const gridCellsEl = document.getElementById('gridCells');
    const occupiedCellsInput = document.getElementById('occupiedCellsInput');
    const previewPaper = document.getElementById('previewPaper');
    const previewSummary = document.getElementById('previewSummary');
    const toggleAllBtn = document.getElementById('toggleAllBtn');
    const clearOccupiedBtn = document.getElementById('clearOccupiedBtn');

    const occupiedSet = new Set(
      (occupiedCellsInput.value || '')
        .split(',')
        .map((value) => Number(value.trim()))
        .filter((value) => Number.isInteger(value) && value > 0)
    );

    function getNumber(name, fallback = 0) {
      const input = form.querySelector(`[name="${name}"]`);
      const value = Number(input ? input.value : fallback);
      return Number.isFinite(value) ? value : fallback;
    }

    function getConfig() {
      return {
        paperWidth: getNumber('paper_width_mm'),
        paperHeight: getNumber('paper_height_mm'),
        rows: Math.max(1, Math.floor(getNumber('rows', 1))),
        cols: Math.max(1, Math.floor(getNumber('cols', 1))),
        marginTop: getNumber('margin_top_mm'),
        marginRight: getNumber('margin_right_mm'),
        marginBottom: getNumber('margin_bottom_mm'),
        marginLeft: getNumber('margin_left_mm'),
        gutterX: getNumber('gutter_x_mm'),
        gutterY: getNumber('gutter_y_mm'),
        fontSize: getNumber('font_size_pt', 7),
      };
    }

    function getSelectedBooks() {
      const selectedIds = Array.from(form.querySelectorAll('.book-checkbox:checked')).map((el) => el.value);
      return selectedIds.map((id) => byId.get(id)).filter(Boolean);
    }

    function syncOccupiedInput() {
      occupiedCellsInput.value = Array.from(occupiedSet).sort((a, b) => a - b).join(',');
    }

    function buildGrid() {
      const config = getConfig();
      const total = config.rows * config.cols;

      Array.from(occupiedSet).forEach((cell) => {
        if (cell > total) {
          occupiedSet.delete(cell);
        }
      });

      syncOccupiedInput();

      gridCellsEl.style.gridTemplateColumns = `repeat(${config.cols}, minmax(24px, 1fr))`;
      gridCellsEl.innerHTML = '';

      const fragment = document.createDocumentFragment();
      for (let i = 1; i <= total; i++) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'label-grid-cell';
        button.dataset.cell = String(i);
        button.textContent = i;

        if (occupiedSet.has(i)) {
          button.classList.add('is-occupied');
        }

        button.addEventListener('click', () => {
          if (occupiedSet.has(i)) {
            occupiedSet.delete(i);
            button.classList.remove('is-occupied');
          } else {
            occupiedSet.add(i);
            button.classList.add('is-occupied');
          }

          syncOccupiedInput();
          renderPreview();
        });

        fragment.appendChild(button);
      }

      gridCellsEl.appendChild(fragment);
    }

    function renderPreview() {
      const config = getConfig();
      const total = config.rows * config.cols;
      const selectedBooks = getSelectedBooks();

      const usableWidth = config.paperWidth - config.marginLeft - config.marginRight - ((config.cols - 1) * config.gutterX);
      const usableHeight = config.paperHeight - config.marginTop - config.marginBottom - ((config.rows - 1) * config.gutterY);

      previewPaper.innerHTML = '';

      if (usableWidth <= 0 || usableHeight <= 0) {
        previewSummary.textContent = 'Konfigurasi tidak valid: margin/gutter melebihi ukuran kertas.';
        return;
      }

      const scale = Math.min(540 / config.paperWidth, 780 / config.paperHeight);
      const cellWidth = usableWidth / config.cols;
      const cellHeight = usableHeight / config.rows;

      previewPaper.style.width = `${config.paperWidth * scale}px`;
      previewPaper.style.height = `${config.paperHeight * scale}px`;

      const slots = [];
      for (let i = 1; i <= total; i++) {
        if (!occupiedSet.has(i)) {
          slots.push(i);
        }
      }

      const printable = Math.min(selectedBooks.length, slots.length);

      for (let i = 0; i < printable; i++) {
        const cellNumber = slots[i];
        const book = selectedBooks[i];
        const index = cellNumber - 1;
        const row = Math.floor(index / config.cols);
        const col = index % config.cols;

        const x = config.marginLeft + (col * (cellWidth + config.gutterX));
        const y = config.marginTop + (row * (cellHeight + config.gutterY));

        const label = document.createElement('div');
        label.className = 'preview-label';
        label.style.left = `${x * scale}px`;
        label.style.top = `${y * scale}px`;
        label.style.width = `${cellWidth * scale}px`;
        label.style.height = `${cellHeight * scale}px`;
        label.style.fontSize = `${Math.max(8, config.fontSize + 1)}px`;

        label.innerHTML = `
          <div class="line-1">${escapeHtml(book.judul || '-')}</div>
          <div class="line-2">${escapeHtml(book.kategori || '-')}</div>
          <div class="line-3">${escapeHtml(book.kode || '-')} | ${escapeHtml(book.pengarang || '-')}</div>
        `;

        previewPaper.appendChild(label);
      }

      const overflow = Math.max(0, selectedBooks.length - slots.length);
      previewSummary.textContent = `Dipilih: ${selectedBooks.length} item | Slot kosong: ${slots.length} dari ${total} | Tercetak: ${printable} | Overflow: ${overflow}`;
    }

    function escapeHtml(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    toggleAllBtn.addEventListener('click', () => {
      const checkboxes = Array.from(form.querySelectorAll('.book-checkbox'));
      const allChecked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
      checkboxes.forEach((checkbox) => {
        checkbox.checked = !allChecked;
      });
      toggleAllBtn.textContent = allChecked ? 'Pilih Semua' : 'Batalkan Semua';
      renderPreview();
    });

    clearOccupiedBtn.addEventListener('click', () => {
      occupiedSet.clear();
      syncOccupiedInput();
      buildGrid();
      renderPreview();
    });

    form.querySelectorAll('.book-checkbox').forEach((checkbox) => {
      checkbox.addEventListener('change', renderPreview);
    });

    form.querySelectorAll('.config-input').forEach((input) => {
      input.addEventListener('input', () => {
        buildGrid();
        renderPreview();
      });
    });

    buildGrid();
    renderPreview();
  })();
</script>
@endpush
