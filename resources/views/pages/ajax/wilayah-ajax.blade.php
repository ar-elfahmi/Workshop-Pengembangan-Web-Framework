@extends('layouts.app')

@section('title', 'Wilayah Dinamis (AJAX)')

@section('content')
<div class="container">
    <div class="page-header">
        <h3 class="page-title">Wilayah Dinamis (jQuery AJAX)</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">AJAX</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wilayah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Dropdown Wilayah Dinamis</h4>
                    <p class="card-description mb-3">
                        Alur: Provinsi -> Kota -> Kecamatan -> Kelurahan
                    </p>

                    <div class="form-group mb-3">
                        <label for="provinsi">Provinsi</label>
                        <select id="provinsi" class="form-control">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kota">Kota</label>
                        <select id="kota" class="form-control" disabled>
                            <option value="">-- Pilih Kota --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kecamatan">Kecamatan</label>
                        <select id="kecamatan" class="form-control" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label for="kelurahan">Kelurahan</label>
                        <select id="kelurahan" class="form-control" disabled>
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message
            });
        }

        function resetKota() {
            $('#kota').html('<option value="">-- Pilih Kota --</option>').prop('disabled', true);
        }

        function resetKecamatan() {
            $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
        }

        function resetKelurahan() {
            $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
        }

        function loadProvinsi() {
            $.ajax({
                url: "{{ route('wilayah.provinsi') }}",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Provinsi response:', response);

                    if (response.status !== 'success') {
                        showError(response.message || 'Gagal mengambil data provinsi');
                        return;
                    }

                    let options = '<option value="">-- Pilih Provinsi --</option>';
                    $.each(response.data, function(_, provinsi) {
                        options += '<option value="' + provinsi.id + '">' + provinsi.name + '</option>';
                    });

                    $('#provinsi').html(options);
                },
                error: function(xhr) {
                    console.log('Provinsi error:', xhr.responseText);
                    showError('Terjadi kesalahan saat memuat provinsi');
                }
            });
        }

        $('#provinsi').on('change', function() {
            const provinsiId = $(this).val();

            // Parent berubah: reset semua child dropdown.
            resetKota();
            resetKecamatan();
            resetKelurahan();

            if (!provinsiId) {
                return;
            }

            $.ajax({
                url: "{{ route('wilayah.kota') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    provinsi_id: provinsiId
                },
                success: function(response) {
                    console.log('Kota response:', response);

                    if (response.status !== 'success') {
                        showError(response.message || 'Gagal mengambil data kota');
                        return;
                    }

                    let options = '<option value="">-- Pilih Kota --</option>';
                    $.each(response.data, function(_, kota) {
                        options += '<option value="' + kota.id + '">' + kota.name + '</option>';
                    });

                    $('#kota').html(options).prop('disabled', false);
                },
                error: function(xhr) {
                    console.log('Kota error:', xhr.responseText);
                    showError('Terjadi kesalahan saat memuat kota');
                }
            });
        });

        $('#kota').on('change', function() {
            const kotaId = $(this).val();

            // Parent berubah: reset semua child dropdown.
            resetKecamatan();
            resetKelurahan();

            if (!kotaId) {
                return;
            }

            $.ajax({
                url: "{{ route('wilayah.kecamatan') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    kota_id: kotaId
                },
                success: function(response) {
                    console.log('Kecamatan response:', response);

                    if (response.status !== 'success') {
                        showError(response.message || 'Gagal mengambil data kecamatan');
                        return;
                    }

                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                    $.each(response.data, function(_, kecamatan) {
                        options += '<option value="' + kecamatan.id + '">' + kecamatan.name + '</option>';
                    });

                    $('#kecamatan').html(options).prop('disabled', false);
                },
                error: function(xhr) {
                    console.log('Kecamatan error:', xhr.responseText);
                    showError('Terjadi kesalahan saat memuat kecamatan');
                }
            });
        });

        $('#kecamatan').on('change', function() {
            const kecamatanId = $(this).val();

            // Parent berubah: reset child dropdown.
            resetKelurahan();

            if (!kecamatanId) {
                return;
            }

            $.ajax({
                url: "{{ route('wilayah.kelurahan') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    kecamatan_id: kecamatanId
                },
                success: function(response) {
                    console.log('Kelurahan response:', response);

                    if (response.status !== 'success') {
                        showError(response.message || 'Gagal mengambil data kelurahan');
                        return;
                    }

                    let options = '<option value="">-- Pilih Kelurahan --</option>';
                    $.each(response.data, function(_, kelurahan) {
                        options += '<option value="' + kelurahan.id + '">' + kelurahan.name + '</option>';
                    });

                    $('#kelurahan').html(options).prop('disabled', false);
                },
                error: function(xhr) {
                    console.log('Kelurahan error:', xhr.responseText);
                    showError('Terjadi kesalahan saat memuat kelurahan');
                }
            });
        });

        loadProvinsi();
    });
</script>
@endpush