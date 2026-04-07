@extends('layouts.app')

@section('title', 'Kasir POS (AJAX)')

@section('content')
<div class="container">
  <div class="page-header">
    <h3 class="page-title">Halaman Kasir POS (jQuery AJAX)</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">AJAX</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kasir</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Input Barang</h4>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" class="form-control" placeholder="Contoh: BRG001" />
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" id="nama_barang" class="form-control" readonly />
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="harga_barang">Harga Barang</label>
                <input type="text" id="harga_barang" class="form-control" readonly />
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="jumlah_barang">Jumlah</label>
                <input type="number" id="jumlah_barang" class="form-control" value="1" min="1" />
              </div>
            </div>

            <div class="col-md-1 d-flex align-items-end">
              <button type="button" id="btn_tambahkan" class="btn btn-primary w-100 mb-3" disabled>
                Tambahkan
              </button>
            </div>
          </div>

          <hr />

          <h4 class="card-title">Daftar Belanja</h4>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Nama</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Subtotal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="cart_body">
                <tr id="empty_row">
                  <td colspan="6" class="text-center">Belum ada item</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="4" class="text-end">Total</th>
                  <th id="cart_total">Rp 0</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="d-flex justify-content-end">
            <button type="button" id="btn_bayar" class="btn btn-success" disabled>Bayar</button>
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
$(document).ready(function () {
  let selectedItem = null;
  let cartItems = {};

  function formatRupiah(value) {
    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
  }

  function resetInputBarang() {
    selectedItem = null;
    $('#kode_barang').val('').focus();
    $('#nama_barang').val('');
    $('#harga_barang').val('');
    $('#jumlah_barang').val(1);
    validateTambahButton();
  }

  function validateTambahButton() {
    const jumlah = Number($('#jumlah_barang').val());
    const canAdd = !!selectedItem && jumlah > 0;
    $('#btn_tambahkan').prop('disabled', !canAdd);
  }

  function calculateTotal() {
    let total = 0;

    Object.keys(cartItems).forEach(function (kode) {
      total += cartItems[kode].subtotal;
    });

    $('#cart_total').text(formatRupiah(total));
    $('#btn_bayar').prop('disabled', total <= 0);

    return total;
  }

  function renderCartTable() {
    const $tbody = $('#cart_body');
    $tbody.empty();

    const keys = Object.keys(cartItems);

    if (keys.length === 0) {
      $tbody.append('<tr id="empty_row"><td colspan="6" class="text-center">Belum ada item</td></tr>');
      calculateTotal();
      return;
    }

    keys.forEach(function (kode) {
      const item = cartItems[kode];

      const rowHtml =
        '<tr data-kode="' + item.kode + '">' +
          '<td>' + item.kode + '</td>' +
          '<td>' + item.nama + '</td>' +
          '<td>' + formatRupiah(item.harga) + '</td>' +
          '<td style="width: 130px;">' +
            '<input type="number" class="form-control form-control-sm qty-input" min="1" value="' + item.jumlah + '" />' +
          '</td>' +
          '<td class="subtotal-cell">' + formatRupiah(item.subtotal) + '</td>' +
          '<td style="width: 90px;">' +
            '<button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>' +
          '</td>' +
        '</tr>';

      $tbody.append(rowHtml);
    });

    calculateTotal();
  }

  function searchBarangByKode(kode) {
    $.ajax({
      url: "{{ route('pos.find-item') }}",
      method: 'POST',
      dataType: 'json',
      data: {
        _token: "{{ csrf_token() }}",
        kode: kode
      },
      success: function (response) {
        console.log('Find item response:', response);

        if (response.status !== 'success') {
          selectedItem = null;
          $('#nama_barang').val('');
          $('#harga_barang').val('');
          validateTambahButton();

          Swal.fire({
            icon: 'warning',
            title: 'Barang tidak ditemukan',
            text: response.message || 'Kode barang tidak terdaftar'
          });
          return;
        }

        selectedItem = response.data;
        $('#nama_barang').val(selectedItem.nama_barang);
        $('#harga_barang').val(formatRupiah(selectedItem.harga));
        validateTambahButton();
      },
      error: function (xhr) {
        console.log('Find item error:', xhr.responseText);
        selectedItem = null;
        $('#nama_barang').val('');
        $('#harga_barang').val('');
        validateTambahButton();

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Terjadi kesalahan saat mengambil data barang'
        });
      }
    });
  }

  $('#kode_barang').on('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();

      const kode = $(this).val().trim();
      if (!kode) {
        return;
      }

      searchBarangByKode(kode);
    }
  });

  $('#jumlah_barang').on('input', function () {
    validateTambahButton();
  });

  $('#btn_tambahkan').on('click', function () {
    if (!selectedItem) {
      return;
    }

    const jumlah = Number($('#jumlah_barang').val());
    if (jumlah <= 0) {
      validateTambahButton();
      return;
    }

    const kode = selectedItem.kode;

    if (cartItems[kode]) {
      cartItems[kode].jumlah += jumlah;
    } else {
      cartItems[kode] = {
        kode: selectedItem.kode,
        nama: selectedItem.nama_barang,
        harga: Number(selectedItem.harga),
        jumlah: jumlah,
        subtotal: 0
      };
    }

    cartItems[kode].subtotal = cartItems[kode].harga * cartItems[kode].jumlah;

    console.log('Cart items after add:', cartItems);

    renderCartTable();
    resetInputBarang();
  });

  $('#cart_body').on('input', '.qty-input', function () {
    const $row = $(this).closest('tr');
    const kode = $row.data('kode');
    const jumlahBaru = Number($(this).val());

    if (!cartItems[kode]) {
      return;
    }

    if (jumlahBaru <= 0 || Number.isNaN(jumlahBaru)) {
      $(this).val(cartItems[kode].jumlah);
      return;
    }

    cartItems[kode].jumlah = jumlahBaru;
    cartItems[kode].subtotal = cartItems[kode].harga * jumlahBaru;

    $row.find('.subtotal-cell').text(formatRupiah(cartItems[kode].subtotal));
    calculateTotal();

    console.log('Cart items after edit qty:', cartItems);
  });

  $('#cart_body').on('click', '.btn-hapus', function () {
    const $row = $(this).closest('tr');
    const kode = $row.data('kode');

    delete cartItems[kode];

    console.log('Cart items after delete:', cartItems);

    renderCartTable();
  });

  $('#btn_bayar').on('click', function () {
    const itemList = Object.keys(cartItems).map(function (kode) {
      return cartItems[kode];
    });

    const total = calculateTotal();

    if (itemList.length === 0 || total <= 0) {
      return;
    }

    $.ajax({
      url: "{{ route('pos.checkout') }}",
      method: 'POST',
      dataType: 'json',
      data: {
        _token: "{{ csrf_token() }}",
        items: itemList,
        total: total
      },
      success: function (response) {
        console.log('Checkout response:', response);

        if (response.status !== 'success') {
          Swal.fire({
            icon: 'warning',
            title: 'Gagal',
            text: response.message || 'Transaksi gagal disimpan'
          });
          return;
        }

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: response.message
        }).then(function () {
          cartItems = {};
          renderCartTable();
          resetInputBarang();
        });
      },
      error: function (xhr) {
        console.log('Checkout error:', xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Terjadi kesalahan saat menyimpan transaksi'
        });
      }
    });
  });

  validateTambahButton();
  renderCartTable();
});
</script>
@endpush
