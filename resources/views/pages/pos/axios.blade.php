@extends('layouts.app')

@section('title', 'Kasir POS (Axios)')

@section('content')
<div class="container">
    <div class="page-header">
        <h3 class="page-title">Halaman Kasir POS (Axios)</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Axios</a></li>
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
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kodeInput = document.getElementById('kode_barang');
        const namaInput = document.getElementById('nama_barang');
        const hargaInput = document.getElementById('harga_barang');
        const jumlahInput = document.getElementById('jumlah_barang');
        const btnTambahkan = document.getElementById('btn_tambahkan');
        const btnBayar = document.getElementById('btn_bayar');
        const cartBody = document.getElementById('cart_body');
        const cartTotal = document.getElementById('cart_total');

        let selectedItem = null;
        let cartItems = {};

        function formatRupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        }

        function validateTambahButton() {
            const jumlah = Number(jumlahInput.value);
            btnTambahkan.disabled = !(selectedItem && jumlah > 0);
        }

        function resetInputBarang() {
            selectedItem = null;
            kodeInput.value = '';
            namaInput.value = '';
            hargaInput.value = '';
            jumlahInput.value = 1;
            validateTambahButton();
            kodeInput.focus();
        }

        function calculateTotal() {
            let total = 0;

            Object.keys(cartItems).forEach(function(kode) {
                total += cartItems[kode].subtotal;
            });

            cartTotal.textContent = formatRupiah(total);
            btnBayar.disabled = total <= 0;

            return total;
        }

        function renderCartTable() {
            cartBody.innerHTML = '';

            const keys = Object.keys(cartItems);
            if (keys.length === 0) {
                cartBody.innerHTML = '<tr id="empty_row"><td colspan="6" class="text-center">Belum ada item</td></tr>';
                calculateTotal();
                return;
            }

            keys.forEach(function(kode) {
                const item = cartItems[kode];

                const row = document.createElement('tr');
                row.setAttribute('data-kode', item.kode);
                row.innerHTML =
                    '<td>' + item.kode + '</td>' +
                    '<td>' + item.nama + '</td>' +
                    '<td>' + formatRupiah(item.harga) + '</td>' +
                    '<td style="width: 130px;">' +
                    '<input type="number" class="form-control form-control-sm qty-input" min="1" value="' + item.jumlah + '" />' +
                    '</td>' +
                    '<td class="subtotal-cell">' + formatRupiah(item.subtotal) + '</td>' +
                    '<td style="width: 90px;">' +
                    '<button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>' +
                    '</td>';

                cartBody.appendChild(row);
            });

            calculateTotal();
        }

        async function searchBarangByKode(kode) {
            try {
                const response = await axios.post("{{ route('pos.find-item') }}", {
                    _token: "{{ csrf_token() }}",
                    kode: kode
                });

                console.log('Find item response:', response.data);

                if (response.data.status !== 'success') {
                    selectedItem = null;
                    namaInput.value = '';
                    hargaInput.value = '';
                    validateTambahButton();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang tidak ditemukan',
                        text: response.data.message || 'Kode barang tidak terdaftar'
                    });
                    return;
                }

                selectedItem = response.data.data;
                namaInput.value = selectedItem.nama_barang;
                hargaInput.value = formatRupiah(selectedItem.harga);
                validateTambahButton();
            } catch (error) {
                console.log('Find item error:', error.response || error.message);
                selectedItem = null;
                namaInput.value = '';
                hargaInput.value = '';
                validateTambahButton();

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengambil data barang'
                });
            }
        }

        kodeInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const kode = kodeInput.value.trim();
                if (!kode) {
                    return;
                }
                searchBarangByKode(kode);
            }
        });

        jumlahInput.addEventListener('input', function() {
            validateTambahButton();
        });

        btnTambahkan.addEventListener('click', function() {
            if (!selectedItem) {
                return;
            }

            const jumlah = Number(jumlahInput.value);
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

        cartBody.addEventListener('input', function(event) {
            if (!event.target.classList.contains('qty-input')) {
                return;
            }

            const row = event.target.closest('tr');
            const kode = row.getAttribute('data-kode');
            const jumlahBaru = Number(event.target.value);

            if (!cartItems[kode]) {
                return;
            }

            if (jumlahBaru <= 0 || Number.isNaN(jumlahBaru)) {
                event.target.value = cartItems[kode].jumlah;
                return;
            }

            cartItems[kode].jumlah = jumlahBaru;
            cartItems[kode].subtotal = cartItems[kode].harga * jumlahBaru;

            row.querySelector('.subtotal-cell').textContent = formatRupiah(cartItems[kode].subtotal);
            calculateTotal();

            console.log('Cart items after edit qty:', cartItems);
        });

        cartBody.addEventListener('click', function(event) {
            if (!event.target.classList.contains('btn-hapus')) {
                return;
            }

            const row = event.target.closest('tr');
            const kode = row.getAttribute('data-kode');

            delete cartItems[kode];
            console.log('Cart items after delete:', cartItems);

            renderCartTable();
        });

        btnBayar.addEventListener('click', async function() {
            const itemList = Object.keys(cartItems).map(function(kode) {
                return cartItems[kode];
            });

            const total = calculateTotal();

            if (itemList.length === 0 || total <= 0) {
                return;
            }

            try {
                const response = await axios.post("{{ route('pos.checkout') }}", {
                    _token: "{{ csrf_token() }}",
                    items: itemList,
                    total: total
                });

                console.log('Checkout response:', response.data);

                if (response.data.status !== 'success') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.data.message || 'Transaksi gagal disimpan'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.data.message
                }).then(function() {
                    cartItems = {};
                    renderCartTable();
                    resetInputBarang();
                });
            } catch (error) {
                console.log('Checkout error:', error.response || error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan transaksi'
                });
            }
        });

        validateTambahButton();
        renderCartTable();
    });
</script>
@endpush