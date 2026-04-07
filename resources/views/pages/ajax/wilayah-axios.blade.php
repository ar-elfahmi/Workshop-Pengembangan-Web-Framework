@extends('layouts.app')

@section('title', 'Wilayah Dinamis (Axios)')

@section('content')
<div class="container">
  <div class="page-header">
    <h3 class="page-title">Wilayah Dinamis (Axios)</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Axios</a></li>
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
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const provinsiSelect = document.getElementById('provinsi');
  const kotaSelect = document.getElementById('kota');
  const kecamatanSelect = document.getElementById('kecamatan');
  const kelurahanSelect = document.getElementById('kelurahan');

  function showError(message) {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: message
    });
  }

  function setOptions(selectEl, placeholder, data) {
    let options = '<option value="">' + placeholder + '</option>';

    data.forEach(function (item) {
      options += '<option value="' + item.id + '">' + item.name + '</option>';
    });

    selectEl.innerHTML = options;
    selectEl.disabled = data.length === 0;
  }

  function resetKota() {
    setOptions(kotaSelect, '-- Pilih Kota --', []);
  }

  function resetKecamatan() {
    setOptions(kecamatanSelect, '-- Pilih Kecamatan --', []);
  }

  function resetKelurahan() {
    setOptions(kelurahanSelect, '-- Pilih Kelurahan --', []);
  }

  async function loadProvinsi() {
    try {
      const response = await axios.get("{{ route('wilayah.provinsi') }}");
      console.log('Provinsi response:', response.data);

      if (response.data.status !== 'success') {
        showError(response.data.message || 'Gagal mengambil data provinsi');
        return;
      }

      setOptions(provinsiSelect, '-- Pilih Provinsi --', response.data.data || []);
    } catch (error) {
      console.log('Provinsi error:', error.response || error.message);
      showError('Terjadi kesalahan saat memuat provinsi');
    }
  }

  provinsiSelect.addEventListener('change', async function () {
    const provinsiId = provinsiSelect.value;

    resetKota();
    resetKecamatan();
    resetKelurahan();

    if (!provinsiId) {
      return;
    }

    try {
      const response = await axios.post("{{ route('wilayah.kota') }}", {
        _token: "{{ csrf_token() }}",
        provinsi_id: provinsiId
      });

      console.log('Kota response:', response.data);

      if (response.data.status !== 'success') {
        showError(response.data.message || 'Gagal mengambil data kota');
        return;
      }

      setOptions(kotaSelect, '-- Pilih Kota --', response.data.data || []);
    } catch (error) {
      console.log('Kota error:', error.response || error.message);
      showError('Terjadi kesalahan saat memuat kota');
    }
  });

  kotaSelect.addEventListener('change', async function () {
    const kotaId = kotaSelect.value;

    resetKecamatan();
    resetKelurahan();

    if (!kotaId) {
      return;
    }

    try {
      const response = await axios.post("{{ route('wilayah.kecamatan') }}", {
        _token: "{{ csrf_token() }}",
        kota_id: kotaId
      });

      console.log('Kecamatan response:', response.data);

      if (response.data.status !== 'success') {
        showError(response.data.message || 'Gagal mengambil data kecamatan');
        return;
      }

      setOptions(kecamatanSelect, '-- Pilih Kecamatan --', response.data.data || []);
    } catch (error) {
      console.log('Kecamatan error:', error.response || error.message);
      showError('Terjadi kesalahan saat memuat kecamatan');
    }
  });

  kecamatanSelect.addEventListener('change', async function () {
    const kecamatanId = kecamatanSelect.value;

    resetKelurahan();

    if (!kecamatanId) {
      return;
    }

    try {
      const response = await axios.post("{{ route('wilayah.kelurahan') }}", {
        _token: "{{ csrf_token() }}",
        kecamatan_id: kecamatanId
      });

      console.log('Kelurahan response:', response.data);

      if (response.data.status !== 'success') {
        showError(response.data.message || 'Gagal mengambil data kelurahan');
        return;
      }

      setOptions(kelurahanSelect, '-- Pilih Kelurahan --', response.data.data || []);
    } catch (error) {
      console.log('Kelurahan error:', error.response || error.message);
      showError('Terjadi kesalahan saat memuat kelurahan');
    }
  });

  loadProvinsi();
});
</script>
@endpush
