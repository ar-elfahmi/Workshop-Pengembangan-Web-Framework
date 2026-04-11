@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- CARD 1: SELECT BIASA --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title text-white mb-0">Select Biasa</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="inputKota" class="form-label fw-bold">Masukkan Kota</label>
                        <div class="input-group">
                            <input
                                type="text"
                                id="inputKota"
                                class="form-control"
                                placeholder="Contoh: Jakarta"
                                style="border-radius: 4px 0 0 4px;">
                            <button
                                type="button"
                                id="btnTambahKota"
                                class="btn btn-primary"
                                style="border-radius: 0 4px 4px 0;">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="selectKota" class="form-label fw-bold">Pilih Kota</label>
                        <select id="selectKota" class="form-control form-select">
                            <option value="">-- Pilih Kota --</option>
                        </select>
                    </div>

                    <div class="alert alert-info border-0 rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-information-outline me-2" style="font-size: 20px;"></i>
                            <div>
                                <strong>Kota Terpilih:</strong>
                                <div id="kotaTerpilih" class="mt-1 ps-3 text-muted">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: SELECT2 --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success">
                    <h5 class="card-title text-white mb-0">Select2 Enhanced</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="inputKota2" class="form-label fw-bold">Masukkan Kota</label>
                        <div class="input-group">
                            <input
                                type="text"
                                id="inputKota2"
                                class="form-control"
                                placeholder="Contoh: Bandung"
                                style="border-radius: 4px 0 0 4px;">
                            <button
                                type="button"
                                id="btnTambahKota2"
                                class="btn btn-success"
                                style="border-radius: 0 4px 4px 0;">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="selectKota2" class="form-label fw-bold">Pilih Kota</label>
                        <select id="selectKota2" class="form-control form-select select2-single" style="width: 100%;">
                            <option value="">-- Pilih Kota --</option>
                        </select>
                    </div>

                    <div class="alert alert-success border-0 rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-check-circle-outline me-2" style="font-size: 20px;"></i>
                            <div>
                                <strong>Kota Terpilih:</strong>
                                <div id="kotaTerpilih2" class="mt-1 ps-3 text-muted">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .card-header.bg-primary,
    .card-header.bg-success {
        border-radius: 4px 4px 0 0;
    }

    .form-control,
    .form-select {
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    $(document).ready(function() {

        // ======== CARD 1: SELECT BIASA ========

        // Tambah Kota
        $('#btnTambahKota').on('click', function() {
            const kota = $('#inputKota').val().trim();

            if (kota === '') {
                alert('Kota tidak boleh kosong!');
                return;
            }

            // Disable button & show loader
            const btn = $(this);
            btn.prop('disabled', true);
            const originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Simulate process
            setTimeout(function() {
                const select = $('#selectKota');
                const isDuplicate = select.find('option').filter(function() {
                    return $(this).val() === kota;
                }).length > 0;

                if (isDuplicate) {
                    alert('Kota sudah ada!');
                } else {
                    select.append($('<option></option>').attr('value', kota).text(kota));
                }

                $('#inputKota').val('');

                btn.prop('disabled', false);
                btn.html(originalText);
            }, 1000);
        });

        // Change event untuk Select
        $('#selectKota').on('change', function() {
            const selected = $(this).val();
            if (selected === '') {
                $('#kotaTerpilih').html('-');
            } else {
                $('#kotaTerpilih').html('<strong>' + selected + '</strong>');
            }
        });

        // Enter key untuk input Kota
        $('#inputKota').on('keypress', function(e) {
            if (e.which === 13) {
                $('#btnTambahKota').click();
            }
        });

        // ======== CARD 2: SELECT2 ========

        // Initialize Select2
        $('#selectKota2').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Kota --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return 'Tidak ada hasil';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });

        // Tambah Kota Select2
        $('#btnTambahKota2').on('click', function() {
            const kota = $('#inputKota2').val().trim();

            if (kota === '') {
                alert('Kota tidak boleh kosong!');
                return;
            }

            // Disable button & show loader
            const btn = $(this);
            btn.prop('disabled', true);
            const originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Simulate process
            setTimeout(function() {
                const select = $('#selectKota2');
                const isDuplicate = select.find('option').filter(function() {
                    return $(this).val() === kota;
                }).length > 0;

                if (isDuplicate) {
                    alert('Kota sudah ada!');
                } else {
                    select.append($('<option></option>').attr('value', kota).text(kota));
                    select.trigger('change');
                }

                $('#inputKota2').val('');

                btn.prop('disabled', false);
                btn.html(originalText);
            }, 1000);
        });

        // Change event untuk Select2
        $('#selectKota2').on('change', function() {
            const selected = $(this).val();
            if (selected === '' || selected === null) {
                $('#kotaTerpilih2').html('-');
            } else {
                $('#kotaTerpilih2').html('<strong>' + selected + '</strong>');
            }
        });

        // Enter key untuk input Kota Select2
        $('#inputKota2').on('keypress', function(e) {
            if (e.which === 13) {
                $('#btnTambahKota2').click();
            }
        });
    });
</script>
@endpush