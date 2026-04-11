<!-- partial:{{ asset('purple-free/dist/partials/_sidebar.html')}} -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  @php
  $user = auth()->user();
  $role = $user->role ?? 'guest';
  @endphp
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('purple-free/dist/assets/images/faces/face1.jpg')}}" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ $user->name ?? 'Guest User' }}</span>
          <span class="text-secondary text-small text-uppercase">{{ $role }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    @if ($role === 'vendor')
    <li class="nav-item">
      <a class="nav-link" href="{{ route('vendor.menu') }}">
        <span class="menu-title">Tambah Menu</span>
        <i class="mdi mdi-food menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('vendor.orders') }}">
        <span class="menu-title">Lihat Pesanan</span>
        <i class="mdi mdi-receipt menu-icon"></i>
      </a>
    </li>
    @endif

    @if ($role !== 'vendor')
    <li class="nav-item">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('buku') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-table-large menu-icon"></i>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link" href="{{ route('buku.labels.index') }}">
        <span class="menu-title">Cetak Label</span>
        <i class="mdi mdi-printer menu-icon"></i>
      </a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link" href="{{ route('reports.index') }}">
        <span class="menu-title">Reports</span>
        <i class="mdi mdi-file-chart menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('wilayah.ajax.page') }}">
        <span class="menu-title">Wilayah AJAX</span>
        <i class="mdi mdi-map-marker menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('wilayah.axios.page') }}">
        <span class="menu-title">Wilayah Axios</span>
        <i class="mdi mdi-map menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('pos.ajax.page') }}">
        <span class="menu-title">POS AJAX</span>
        <i class="mdi mdi-cart menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('pos.axios.page') }}">
        <span class="menu-title">POS Axios</span>
        <i class="mdi mdi-cash menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#barang-menu" aria-expanded="false" aria-controls="barang-menu">
        <span class="menu-title">Barang</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-inbox-multiple menu-icon"></i>
      </a>
      <div class="collapse" id="barang-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('barang.index') }}">Basic Table</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('barang.datatables') }}">DataTables</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('barang.select') }}">Select & Select2</a>
          </li>
        </ul>
      </div>
    </li>
    @endif
  </ul>
</nav>
<!-- partial -->


<script>
  $(document).ready(function() {
    const currentPath = window.location.pathname;

    // Set active class on nav links based on current path
    $('.nav-link').each(function() {
      const href = $(this).attr('href');

      // Skip placeholder links
      if (!href || href === '#') {
        return;
      }

      // Support both relative links and absolute URLs from route()
      let linkPath = href;
      if (href.startsWith('http')) {
        linkPath = new URL(href, window.location.origin).pathname;
      }

      // Check if current path matches this link (remove trailing slashes for comparison)
      linkPath = linkPath.replace(/\/$/, '');
      const pathToCheck = currentPath.replace(/\/$/, '');

      if (pathToCheck.includes(linkPath) || linkPath.includes(pathToCheck.split('/').pop())) {
        $(this).addClass('active');

        // If this is a sub-menu item, also mark parent as active and expand it
        const parentCollapse = $(this).closest('.collapse');
        if (parentCollapse.length) {
          const collapseId = parentCollapse.attr('id');
          const parentLink = $('a[href="#' + collapseId + '"]');

          if (parentLink.length) {
            parentLink.addClass('active');
            parentLink.attr('aria-expanded', 'true');
            parentCollapse.addClass('show');
          }
        }
      }
    });
  });
</script>