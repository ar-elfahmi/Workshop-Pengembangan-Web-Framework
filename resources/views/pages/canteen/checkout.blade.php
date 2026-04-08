<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Kantin</title>
    <style>
        :root {
            --bg-0: #fdf7ef;
            --bg-1: #f9e7c7;
            --surface: #fffdf8;
            --ink: #1f1f1f;
            --muted: #6d6a66;
            --accent: #0f766e;
            --accent-2: #d97706;
            --danger: #b91c1c;
            --line: #e9decf;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(900px 380px at 10% -10%, #ffe8b9 0%, transparent 70%),
                radial-gradient(800px 340px at 95% 0%, #ffd7bd 0%, transparent 65%),
                linear-gradient(180deg, var(--bg-0), var(--bg-1));
            min-height: 100vh;
        }

        .wrap {
            width: min(1080px, 92vw);
            margin: 24px auto 36px;
            display: grid;
            gap: 18px;
        }

        .head {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(30, 20, 10, 0.07);
        }

        .head h1 {
            margin: 0;
            font-size: clamp(24px, 3.8vw, 34px);
            letter-spacing: 0.2px;
        }

        .head p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: 1.2fr 0.8fr;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 8px 20px rgba(30, 20, 10, 0.05);
        }

        .field {
            margin-bottom: 12px;
        }

        .field label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .input,
        .select,
        .textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fff;
        }

        .textarea {
            min-height: 64px;
            resize: vertical;
        }

        .row {
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr 120px;
        }

        .btn {
            border: none;
            border-radius: 11px;
            padding: 11px 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-main {
            background: var(--accent);
            color: #fff;
        }

        .btn-alt {
            background: var(--accent-2);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .line {
            height: 1px;
            background: var(--line);
            margin: 14px 0;
        }

        .cart-list {
            display: grid;
            gap: 10px;
            max-height: 310px;
            overflow: auto;
            padding-right: 2px;
        }

        .item {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            background: #fff;
        }

        .item h4 {
            margin: 0;
            font-size: 14px;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .pay-method {
            display: grid;
            gap: 8px;
            margin: 8px 0 14px;
        }

        .status {
            margin-top: 8px;
            font-size: 13px;
            color: var(--muted);
            min-height: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .vendor-card,
        .menu-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .vendor-card:hover,
        .menu-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 20, 10, 0.08);
        }

        .vendor-card.active,
        .menu-card.active {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.14);
        }

        .menu-footer {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .qty-btn {
            border: 1px solid var(--line);
            background: #fff;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        @media (max-width: 880px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .wrap {
                margin-top: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <section class="head">
            <h1>Kantin Online Checkout</h1>
            <p>Pilih vendor lewat card, tambahkan menu ke keranjang, lalu bayar via VA atau QRIS.</p>
        </section>

        <section class="grid">
            <article class="card">
                <h3>Pilih Vendor</h3>
                <div id="vendorCards" class="cards">
                    <div class="muted">Memuat vendor...</div>
                </div>

                <div class="line"></div>

                <h3>Pilih Menu</h3>
                <div id="menuCards" class="cards">
                    <div class="muted">Pilih vendor dulu untuk menampilkan menu.</div>
                </div>

                <div class="field">
                    <label for="noteInput">Catatan</label>
                    <textarea id="noteInput" class="textarea" placeholder="Contoh: sambal dipisah"></textarea>
                </div>
            </article>

            <article class="card">
                <h3>Keranjang & Bayar</h3>
                <div id="cartList" class="cart-list"></div>

                <div class="line"></div>

                <div class="muted">Total</div>
                <h2 id="totalText" style="margin: 4px 0 10px;">Rp 0</h2>

                <div class="pay-method">
                    <label><input type="radio" name="metodeBayar" value="va" checked> Virtual Account</label>
                    <label><input type="radio" name="metodeBayar" value="qris"> QRIS</label>
                </div>

                <button id="checkoutBtn" class="btn btn-alt" type="button">Checkout & Bayar</button>
                <div id="statusText" class="status"></div>
            </article>
        </section>
    </div>

    <script
        src="{{ $midtransIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        const vendorCards = document.getElementById('vendorCards');
        const menuCards = document.getElementById('menuCards');
        const noteInput = document.getElementById('noteInput');
        const cartList = document.getElementById('cartList');
        const totalText = document.getElementById('totalText');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const statusText = document.getElementById('statusText');

        let vendors = [];
        let menus = [];
        let cart = [];
        let selectedVendorId = null;
        let selectedMenuId = null;
        let menuQtyMap = {};

        function rupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(value);
        }

        function getSelectedPaymentMethod() {
            const selected = document.querySelector('input[name="metodeBayar"]:checked');
            return selected ? selected.value : 'va';
        }

        function setStatus(message, isError = false) {
            statusText.textContent = message;
            statusText.style.color = isError ? '#b91c1c' : '#4b5563';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function renderVendorCards() {
            vendorCards.innerHTML = '';

            if (!vendors.length) {
                vendorCards.innerHTML = '<div class="muted">Vendor belum tersedia.</div>';
                return;
            }

            vendors.forEach((vendor) => {
                const card = document.createElement('div');
                card.className = `vendor-card ${selectedVendorId === vendor.idvendor ? 'active' : ''}`;
                card.innerHTML = `<h4>${escapeHtml(vendor.nama_vendor)}</h4><div class="muted">Klik untuk lihat menu</div>`;

                card.addEventListener('click', () => {
                    selectedVendorId = vendor.idvendor;
                    selectedMenuId = null;
                    cart = [];
                    menuQtyMap = {};
                    renderVendorCards();
                    renderCart();
                    loadMenus(vendor.idvendor).catch(() => setStatus('Gagal memuat menu vendor.', true));
                });

                vendorCards.appendChild(card);
            });
        }

        function renderMenuCards() {
            menuCards.innerHTML = '';

            if (!selectedVendorId) {
                menuCards.innerHTML = '<div class="muted">Pilih vendor dulu untuk menampilkan menu.</div>';
                return;
            }

            if (!menus.length) {
                menuCards.innerHTML = '<div class="muted">Vendor ini belum punya menu.</div>';
                return;
            }

            menus.forEach((menu) => {
                const qty = menuQtyMap[menu.idmenu] || 1;

                const card = document.createElement('div');
                card.className = `menu-card ${selectedMenuId === menu.idmenu ? 'active' : ''}`;
                card.innerHTML = `
                    <h4>${escapeHtml(menu.nama_menu)}</h4>
                    <div class="muted">${rupiah(menu.harga)}</div>
                    <div class="menu-footer">
                        <div class="qty-control">
                            <button class="qty-btn" data-action="minus">-</button>
                            <span>${qty}</span>
                            <button class="qty-btn" data-action="plus">+</button>
                        </div>
                        <button class="btn btn-main" type="button" style="padding:7px 10px;">Tambah</button>
                    </div>
                `;

                card.addEventListener('click', () => {
                    selectedMenuId = menu.idmenu;
                    renderMenuCards();
                });

                card.querySelectorAll('.qty-btn').forEach((button) => {
                    button.addEventListener('click', (event) => {
                        event.stopPropagation();
                        const action = button.getAttribute('data-action');
                        const current = menuQtyMap[menu.idmenu] || 1;

                        if (action === 'minus') {
                            menuQtyMap[menu.idmenu] = Math.max(1, current - 1);
                        }

                        if (action === 'plus') {
                            menuQtyMap[menu.idmenu] = current + 1;
                        }

                        renderMenuCards();
                    });
                });

                card.querySelector('.btn-main').addEventListener('click', (event) => {
                    event.stopPropagation();
                    addMenuToCart(menu.idmenu);
                });

                menuCards.appendChild(card);
            });
        }

        function addMenuToCart(menuId) {
            const menu = menus.find((item) => Number(item.idmenu) === Number(menuId));
            if (!menu) {
                setStatus('Menu tidak ditemukan.', true);
                return;
            }

            const qty = Math.max(1, Number(menuQtyMap[menu.idmenu] || 1));
            const catatan = (noteInput.value || '').trim();
            const harga = Number(menu.harga);

            const existingIndex = cart.findIndex((item) => item.idmenu === menu.idmenu && (item.catatan || '') === catatan);

            if (existingIndex >= 0) {
                cart[existingIndex].jumlah += qty;
                cart[existingIndex].subtotal = cart[existingIndex].jumlah * cart[existingIndex].harga;
            } else {
                cart.push({
                    idmenu: Number(menu.idmenu),
                    nama_menu: menu.nama_menu,
                    harga,
                    jumlah: qty,
                    subtotal: harga * qty,
                    catatan,
                });
            }

            noteInput.value = '';
            menuQtyMap[menu.idmenu] = 1;
            renderMenuCards();
            renderCart();
            setStatus('Menu ditambahkan ke keranjang.');
        }

        function renderCart() {
            cartList.innerHTML = '';

            if (cart.length === 0) {
                cartList.innerHTML = '<div class="muted">Keranjang masih kosong.</div>';
                totalText.textContent = rupiah(0);
                return;
            }

            let total = 0;

            cart.forEach((item, index) => {
                total += item.subtotal;

                const div = document.createElement('div');
                div.className = 'item';
                div.innerHTML = `
                <div>
                    <h4>${item.nama_menu}</h4>
                    <div class="muted">${item.jumlah} x ${rupiah(item.harga)} = ${rupiah(item.subtotal)}</div>
                    ${item.catatan ? `<div class="muted">Catatan: ${item.catatan}</div>` : ''}
                </div>
                <button class="btn btn-danger" type="button" data-index="${index}">Hapus</button>
            `;
                cartList.appendChild(div);
            });

            totalText.textContent = rupiah(total);

            cartList.querySelectorAll('button[data-index]').forEach((button) => {
                button.addEventListener('click', () => {
                    const index = Number(button.getAttribute('data-index'));
                    cart.splice(index, 1);
                    renderCart();
                });
            });
        }

        async function loadVendors() {
            const response = await fetch('/api/canteen/vendors');
            const result = await response.json();
            vendors = result.data || [];
            renderVendorCards();
        }

        async function loadMenus(vendorId) {
            menuCards.innerHTML = '<div class="muted">Memuat menu...</div>';

            const response = await fetch(`/api/canteen/vendors/${vendorId}/menus`);
            const result = await response.json();

            menus = result.data.menus || [];
            renderMenuCards();
        }

        checkoutBtn.addEventListener('click', async () => {
            const vendorId = Number(selectedVendorId || 0);

            if (!vendorId) {
                setStatus('Pilih vendor lebih dulu.', true);
                return;
            }

            if (cart.length === 0) {
                setStatus('Keranjang masih kosong.', true);
                return;
            }

            checkoutBtn.disabled = true;
            setStatus('Membuat pesanan...');

            try {
                const payload = {
                    idvendor: vendorId,
                    metode_bayar: getSelectedPaymentMethod(),
                    items: cart.map((item) => ({
                        idmenu: item.idmenu,
                        jumlah: item.jumlah,
                        catatan: item.catatan || null,
                    })),
                };

                const response = await fetch('/api/canteen/orders', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal membuat pesanan.');
                }

                const snapToken = result.data?.payment?.snap_token;
                if (!snapToken) {
                    throw new Error('Snap token tidak tersedia.');
                }

                setStatus('Pesanan dibuat. Membuka popup pembayaran...');

                window.snap.pay(snapToken, {
                    onSuccess: function() {
                        setStatus('Pembayaran berhasil. Status akan menjadi Lunas setelah callback Midtrans.');
                        cart = [];
                        renderCart();
                    },
                    onPending: function() {
                        setStatus('Pembayaran masih pending. Selesaikan di channel pembayaran kamu.');
                    },
                    onError: function() {
                        setStatus('Pembayaran gagal diproses.', true);
                    },
                    onClose: function() {
                        setStatus('Popup pembayaran ditutup. Kamu bisa coba lagi.');
                    }
                });
            } catch (error) {
                setStatus(error.message || 'Terjadi kesalahan saat checkout.', true);
            } finally {
                checkoutBtn.disabled = false;
            }
        });

        loadVendors()
            .then(() => setStatus('Siap menerima pesanan.'))
            .catch(() => setStatus('Gagal memuat vendor.', true));

        renderCart();
    </script>
</body>

</html>