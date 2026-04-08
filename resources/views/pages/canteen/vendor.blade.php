<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Kantin</title>
    <style>
        :root {
            --bg: #f4f6ea;
            --panel: #ffffff;
            --ink: #162313;
            --muted: #657164;
            --line: #d9e2ce;
            --accent: #2f7d32;
            --accent-2: #1b5e20;
            --danger: #b42318;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(900px 300px at 8% -12%, #d5f2be 0%, transparent 72%),
                radial-gradient(800px 280px at 100% 0%, #e8f7d6 0%, transparent 70%),
                var(--bg);
            color: var(--ink);
            min-height: 100vh;
        }

        .wrap {
            width: min(1100px, 92vw);
            margin: 24px auto 38px;
            display: grid;
            gap: 18px;
        }

        .hero,
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(22, 35, 19, 0.05);
        }

        .hero {
            padding: 18px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(22px, 3.2vw, 32px);
        }

        .hero p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: 0.95fr 1.05fr;
        }

        .panel {
            padding: 16px;
        }

        h3 {
            margin: 0 0 12px;
        }

        .field {
            margin-bottom: 12px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .input,
        .select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fff;
        }

        .row {
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr 150px;
        }

        .btn {
            border: 0;
            border-radius: 11px;
            padding: 11px 14px;
            cursor: pointer;
            color: #fff;
            font-weight: 600;
        }

        .btn-main {
            background: var(--accent);
        }

        .btn-main:hover {
            background: var(--accent-2);
        }

        .status {
            min-height: 20px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 640px;
            background: #fff;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            vertical-align: top;
        }

        th {
            font-size: 12px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #4f5d4e;
            background: #f8fbf3;
        }

        .badge {
            display: inline-block;
            font-size: 12px;
            color: #0b6e2d;
            background: #ddf7e3;
            border: 1px solid #bdecc8;
            padding: 3px 8px;
            border-radius: 999px;
            font-weight: 600;
        }

        .empty {
            padding: 16px;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 920px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <section class="hero">
            <h1>Vendor Kantin</h1>
            <p>Kelola menu vendor dan lihat daftar pesanan yang sudah Lunas.</p>
        </section>

        <section class="grid">
            <article class="panel">
                <h3>Tambah Master Menu</h3>

                <div class="field">
                    <label for="vendorSelectMenu">Vendor</label>
                    <select id="vendorSelectMenu" class="select">
                        <option value="">Memuat vendor...</option>
                    </select>
                </div>

                <div class="field">
                    <label for="namaMenuInput">Nama Menu</label>
                    <input id="namaMenuInput" class="input" type="text" placeholder="Contoh: Nasi Bakar Ayam">
                </div>

                <div class="row">
                    <div class="field">
                        <label for="hargaInput">Harga</label>
                        <input id="hargaInput" class="input" type="number" min="1" placeholder="15000">
                    </div>
                    <div class="field">
                        <label>&nbsp;</label>
                        <button id="saveMenuBtn" class="btn btn-main" type="button">Simpan</button>
                    </div>
                </div>

                <div class="field">
                    <label for="gambarInput">Path Gambar (opsional)</label>
                    <input id="gambarInput" class="input" type="text" placeholder="images/menu/nasi-bakar.jpg">
                </div>

                <div id="menuStatus" class="status"></div>
            </article>

            <article class="panel">
                <h3>Pesanan Lunas</h3>

                <div class="field">
                    <label for="vendorSelectOrders">Vendor</label>
                    <select id="vendorSelectOrders" class="select">
                        <option value="">Pilih vendor</option>
                    </select>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Nama Customer</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody id="ordersBody">
                            <tr>
                                <td colspan="6" class="empty">Pilih vendor untuk melihat pesanan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="ordersStatus" class="status"></div>
            </article>
        </section>
    </div>

    <script>
        const vendorSelectMenu = document.getElementById('vendorSelectMenu');
        const vendorSelectOrders = document.getElementById('vendorSelectOrders');
        const namaMenuInput = document.getElementById('namaMenuInput');
        const hargaInput = document.getElementById('hargaInput');
        const gambarInput = document.getElementById('gambarInput');
        const saveMenuBtn = document.getElementById('saveMenuBtn');
        const menuStatus = document.getElementById('menuStatus');
        const ordersBody = document.getElementById('ordersBody');
        const ordersStatus = document.getElementById('ordersStatus');

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(value || 0);
        }

        function setStatus(el, text, isError = false) {
            el.textContent = text;
            el.style.color = isError ? '#b42318' : '#657164';
        }

        function normalizeText(input) {
            return (input || '').replace(/[<>]/g, '');
        }

        function renderOrders(orders) {
            ordersBody.innerHTML = '';

            if (!orders || orders.length === 0) {
                ordersBody.innerHTML = '<tr><td colspan="6" class="empty">Belum ada pesanan Lunas untuk vendor ini.</td></tr>';
                return;
            }

            orders.forEach((order) => {
                const details = (order.details || [])
                    .map((detail) => {
                        const name = normalizeText(detail.menu?.nama_menu || '-');
                        const qty = detail.jumlah || 0;
                        const sub = formatRupiah(detail.subtotal || 0);
                        return `${name} (${qty}) = ${sub}`;
                    })
                    .join('<br>');

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>#${order.idpesanan}</td>
                <td>${normalizeText(order.nama || '-')}</td>
                <td>${formatRupiah(order.total || 0)}</td>
                <td>${String(order.metode_bayar || '-').toUpperCase()}</td>
                <td><span class="badge">${normalizeText(order.status_bayar || '-')}</span></td>
                <td>${details || '-'}</td>
            `;
                ordersBody.appendChild(tr);
            });
        }

        async function loadVendors() {
            const response = await fetch('/api/canteen/vendors');
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengambil vendor.');
            }

            const vendors = result.data || [];

            vendorSelectMenu.innerHTML = '<option value="">Pilih vendor</option>';
            vendorSelectOrders.innerHTML = '<option value="">Pilih vendor</option>';

            vendors.forEach((vendor) => {
                const text = normalizeText(vendor.nama_vendor);

                const optionMenu = document.createElement('option');
                optionMenu.value = String(vendor.idvendor);
                optionMenu.textContent = text;
                vendorSelectMenu.appendChild(optionMenu);

                const optionOrder = document.createElement('option');
                optionOrder.value = String(vendor.idvendor);
                optionOrder.textContent = text;
                vendorSelectOrders.appendChild(optionOrder);
            });
        }

        async function loadPaidOrders(vendorId) {
            ordersBody.innerHTML = '<tr><td colspan="6" class="empty">Memuat data pesanan...</td></tr>';

            const response = await fetch(`/api/canteen/vendors/${vendorId}/orders/lunas`);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengambil data pesanan.');
            }

            renderOrders(result.data || []);
            setStatus(ordersStatus, 'Daftar pesanan Lunas berhasil diperbarui.');
        }

        saveMenuBtn.addEventListener('click', async () => {
            const idvendor = Number(vendorSelectMenu.value || 0);
            const nama_menu = normalizeText(namaMenuInput.value.trim());
            const harga = Number(hargaInput.value || 0);
            const path_gambar = normalizeText(gambarInput.value.trim());

            if (!idvendor) {
                setStatus(menuStatus, 'Pilih vendor dulu.', true);
                return;
            }

            if (!nama_menu) {
                setStatus(menuStatus, 'Nama menu wajib diisi.', true);
                return;
            }

            if (harga < 1) {
                setStatus(menuStatus, 'Harga minimal 1.', true);
                return;
            }

            saveMenuBtn.disabled = true;
            setStatus(menuStatus, 'Menyimpan menu...');

            try {
                const response = await fetch('/api/canteen/vendor/menus', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        idvendor,
                        nama_menu,
                        harga,
                        path_gambar: path_gambar || null,
                    }),
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal menambah menu.');
                }

                namaMenuInput.value = '';
                hargaInput.value = '';
                gambarInput.value = '';
                setStatus(menuStatus, 'Menu baru berhasil ditambahkan.');
            } catch (error) {
                setStatus(menuStatus, error.message || 'Terjadi kesalahan saat menyimpan menu.', true);
            } finally {
                saveMenuBtn.disabled = false;
            }
        });

        vendorSelectOrders.addEventListener('change', () => {
            const idvendor = Number(vendorSelectOrders.value || 0);

            if (!idvendor) {
                ordersBody.innerHTML = '<tr><td colspan="6" class="empty">Pilih vendor untuk melihat pesanan.</td></tr>';
                setStatus(ordersStatus, '');
                return;
            }

            loadPaidOrders(idvendor).catch((error) => {
                setStatus(ordersStatus, error.message || 'Gagal memuat pesanan.', true);
                ordersBody.innerHTML = '<tr><td colspan="6" class="empty">Gagal memuat data pesanan.</td></tr>';
            });
        });

        loadVendors()
            .then(() => setStatus(menuStatus, 'Siap kelola menu vendor.'))
            .catch((error) => setStatus(menuStatus, error.message || 'Gagal memuat vendor.', true));
    </script>
</body>

</html>