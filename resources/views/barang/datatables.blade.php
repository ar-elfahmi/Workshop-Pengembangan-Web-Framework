@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Manajemen Barang (DataTables)</h4>
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

                {{-- TABLE SECTION WITH DATATABLES --}}
                <div class="table-responsive">
                    <table id="tabelBarang" class="table table-hover table-striped">
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<style>
    #tabelBarang {
        width: 100% !important;
    }

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

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.3rem 0.6rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin-left: 0.25rem;
        border: 0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button a,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a {
        border-radius: 0.25rem;
        padding: 0.35rem 0.7rem;
    }
</style>

<script>
    $(document).ready(function() {
        let idCounter = 1;
        let dataBarang = [];
        let editingId = null;
        let table;

        // Initialize DataTable
        table = $('#tabelBarang').DataTable({
            data: [],
            columns: [{
                    data: 'id'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'harga',
                    render: function(data) {
                        return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                    }
                }
            ],
            columnDefs: [{
                targets: '_all',
                className: 'dt-left'
            }],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                },
                emptyTable: 'Belum ada data barang'
            }
        });

        // Click row to edit
        $(document).on('click', '#tabelBarang tbody tr', function() {
            const rowData = table.row(this).data();

            if (rowData) {
                editingId = rowData.id;
                $('#editIdBarang').val(rowData.id);
                $('#editNamaBarang').val(rowData.nama);
                $('#editHargaBarang').val(rowData.harga);
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

                const newRow = {
                    id: idCounter,
                    nama: nama,
                    harga: parseInt(harga)
                };
                idCounter++;

                table.row.add(newRow).draw(false);

                // Reset form
                form.reset();

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
                const selectedRow = table.row(function(idx, data) {
                    return data.id === editingId;
                });

                if (selectedRow.length > 0) {
                    const rowNode = selectedRow.node();
                    const rowObj = selectedRow.data();
                    rowObj.nama = $('#editNamaBarang').val();
                    rowObj.harga = parseInt($('#editHargaBarang').val());

                    table.row(rowNode).data(rowObj).draw(false);
                }

                $('#modalEdit').modal('hide');

                btn.prop('disabled', false);
                btn.html(originalText);
            }, 1500);
        });

        // Hapus Button
        $('#btnHapus').on('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                const selectedRow = table.row(function(idx, data) {
                    return data.id === editingId;
                });

                if (selectedRow.length > 0) {
                    table.row(selectedRow.node()).remove().draw(false);
                }

                $('#modalEdit').modal('hide');
            }
        });

        // Add cursor pointer style
        $(document).on('mouseenter', '#tabelBarang tbody tr', function() {
            $(this).css('cursor', 'pointer');
        });
    });
</script>
@endpush