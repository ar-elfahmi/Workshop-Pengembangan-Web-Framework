@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Menu Vendor</h4>
                <p class="card-description">Tambahkan master menu untuk vendor kamu.</p>

                <form id="vendorMenuForm" class="forms-sample">
                    @csrf
                    <div class="form-group">
                        <label for="nama_menu">Nama Menu</label>
                        <input type="text" class="form-control" id="nama_menu" placeholder="Contoh: Nasi Bakar Ayam" required>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" class="form-control" id="harga" min="1" placeholder="15000" required>
                    </div>

                    <div class="form-group">
                        <label for="path_gambar">Path Gambar (opsional)</label>
                        <input type="text" class="form-control" id="path_gambar" placeholder="images/menu/nasi-bakar.jpg">
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Menu</button>
                    <span id="menuFormStatus" class="ms-2 text-muted"></span>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    const form = document.getElementById('vendorMenuForm');
    const statusEl = document.getElementById('menuFormStatus');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            nama_menu: document.getElementById('nama_menu').value.trim(),
            harga: Number(document.getElementById('harga').value || 0),
            path_gambar: document.getElementById('path_gambar').value.trim() || null,
        };

        if (!payload.nama_menu || payload.harga < 1) {
            statusEl.className = 'ms-2 text-danger';
            statusEl.textContent = 'Isi data menu dengan benar.';
            return;
        }

        statusEl.className = 'ms-2 text-muted';
        statusEl.textContent = 'Menyimpan...';

        try {
            const response = await fetch('{{ route('
                vendor.menu.store ') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify(payload),
                });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Gagal menyimpan menu.');
            }

            form.reset();
            statusEl.className = 'ms-2 text-success';
            statusEl.textContent = 'Menu berhasil ditambahkan.';
        } catch (error) {
            statusEl.className = 'ms-2 text-danger';
            statusEl.textContent = error.message || 'Terjadi kesalahan.';
        }
    });
</script>
@endpush