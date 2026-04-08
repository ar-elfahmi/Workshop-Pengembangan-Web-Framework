@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pesanan Lunas</h4>
                <p class="card-description">Daftar pesanan dengan status pembayaran Lunas.</p>

                <div class="table-responsive">
                    <table class="table table-hover" id="ordersTable">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Detail Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-muted">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <small id="ordersStatus" class="text-muted"></small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    const tableBody = document.querySelector('#ordersTable tbody');
    const statusEl = document.getElementById('ordersStatus');

    function rupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value || 0);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderRows(orders) {
        tableBody.innerHTML = '';

        if (!orders.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-muted">Belum ada pesanan Lunas.</td></tr>';
            return;
        }

        orders.forEach((order) => {
            const detailText = (order.details || [])
                .map((item) => {
                    const name = item.menu?.nama_menu || '-';
                    return `${name} (${item.jumlah})`;
                })
                .join(', ');

            const row = document.createElement('tr');
            row.innerHTML = `
        <td>#${order.idpesanan}</td>
        <td>${escapeHtml(order.nama || '-')}</td>
        <td>${rupiah(order.total)}</td>
        <td>${escapeHtml(String(order.metode_bayar || '-').toUpperCase())}</td>
        <td><label class="badge badge-success">${escapeHtml(order.status_bayar || '-')}</label></td>
        <td>${escapeHtml(detailText || '-')}</td>
      `;
            tableBody.appendChild(row);
        });
    }

    async function loadOrders() {
        statusEl.className = 'text-muted';
        statusEl.textContent = 'Mengambil data pesanan...';

        try {
            const response = await fetch('{{ route('
                vendor.orders.lunas ') }}', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Gagal memuat data pesanan.');
            }

            renderRows(result.data || []);
            statusEl.className = 'text-success';
            statusEl.textContent = 'Data pesanan berhasil dimuat.';
        } catch (error) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-danger">Gagal memuat data.</td></tr>';
            statusEl.className = 'text-danger';
            statusEl.textContent = error.message || 'Terjadi kesalahan.';
        }
    }

    loadOrders();
</script>
@endpush