@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Manajemen Barang</h4>
            </div>
            <div class="card-body">
                {{-- FORM SECTION --}}
                <form id="barangForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="namaBarang">Nama Barang</label>
                                <input type="text" id="namaBarang" class="form-control" placeholder="Masukkan nama barang" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="hargaBarang">Harga Barang</label>
                                <input type="number" id="hargaBarang" class="form-control" placeholder="Masukkan harga" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btnTambah" class="btn btn-primary btn-block">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- TABLE SECTION --}}
                <div class="table-responsive">
                    <table id="tabelBarang" class="table table-hover table-striped table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editIdBarang">ID Barang</label>
                        <input type="text" id="editIdBarang" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNamaBarang">Nama Barang</label>
                        <input type="text" id="editNamaBarang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editHargaBarang">Harga Barang</label>
                        <input type="number" id="editHargaBarang" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" id="btnUbah" class="btn btn-primary">Ubah</button>
                    <button type="button" id="btnHapus" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<style>
    #tabelBarang thead th {
        background-color: #f4f6fc;
        font-weight: 600;
        white-space: nowrap;
    }

    #tabelBarang td,
    #tabelBarang th {
        vertical-align: middle;
    }

    #tabelBarang td:nth-child(1) {
        width: 110px;
        text-align: center;
        font-weight: 600;
    }

    #tabelBarang td:nth-child(3),
    #tabelBarang th:nth-child(3) {
        text-align: right;
        white-space: nowrap;
    }

    #tabelBarang tbody tr {
        cursor: pointer;
    }
</style>

<script>
    $(document).ready(function() {
        let idCounter = 1;
        let dataBarang = [];
        let editingId = null;

        // Render table
        function renderTable() {
            const tbody = $('#tabelBarang tbody');
            tbody.empty();

            dataBarang.forEach(function(barang) {
                const row = `
                <tr class="barang-row" data-id="${barang.id}" style="cursor: pointer;">
                    <td>${barang.id}</td>
                    <td>${barang.nama}</td>
                    <td>Rp ${parseInt(barang.harga).toLocaleString('id-ID')}</td>
                </tr>
            `;
                tbody.append(row);
            });
        }

        // Click row to edit
        $(document).on('click', '.barang-row', function() {
            const id = $(this).data('id');
            const barang = dataBarang.find(b => b.id === id);

            if (barang) {
                editingId = id;
                $('#editIdBarang').val(barang.id);
                $('#editNamaBarang').val(barang.nama);
                $('#editHargaBarang').val(barang.harga);
                $('#modalEdit').modal('show');
            }
        });

        // Tambah Button
        $('#btnTambah').on('click', function() {
            const form = document.getElementById('barangForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Disable button & show loader
            const btn = $(this);
            btn.prop('disabled', true);
            const originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Simulate process
            setTimeout(function() {
                const nama = $('#namaBarang').val();
                const harga = $('#hargaBarang').val();

                dataBarang.push({
                    id: idCounter,
                    nama: nama,
                    harga: parseInt(harga)
                });
                idCounter++;

                // Reset form
                form.reset();

                // Re-render table
                renderTable();

                // Re-enable button
                btn.prop('disabled', false);
                btn.html(originalText);
            }, 1500);
        });

        // Ubah Button
        $('#btnUbah').on('click', function() {
            const form = document.getElementById('formEdit');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true);
            const originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            setTimeout(function() {
                const barang = dataBarang.find(b => b.id === editingId);
                if (barang) {
                    barang.nama = $('#editNamaBarang').val();
                    barang.harga = parseInt($('#editHargaBarang').val());
                }

                renderTable();
                $('#modalEdit').modal('hide');

                btn.prop('disabled', false);
                btn.html(originalText);
            }, 1500);
        });

        // Hapus Button
        $('#btnHapus').on('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                dataBarang = dataBarang.filter(b => b.id !== editingId);
                renderTable();
                $('#modalEdit').modal('hide');
            }
        });
    });
</script>
@endpush