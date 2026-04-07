@extends('layouts.app')

@section('title', 'Reports')

@section('content')
  <div class="container">
    <div class="page-header">
      <h3 class="page-title">Reports</h3>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Pages</a></li>
          <li class="breadcrumb-item active" aria-current="page">Reports</li>
        </ol>
      </nav>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Laporan Ringkasan</h4>
              <a href="{{ url('/reports/pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="mdi mdi-file-pdf"></i> Download PDF (Landscape)
              </a>
            </div>

            <p class="text-muted">Halaman ini menampilkan laporan ringkasan data buku per kategori.</p>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kategori</th>
                  <th>Jumlah Buku</th>
                  <th>Daftar Judul</th>
                  <th>Daftar Pengarang</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($reports as $index => $report)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report['kategori'] }}</td>
                    <td>{{ $report['jumlah_buku'] }}</td>
                    <td>{{ $report['daftar_judul'] }}</td>
                    <td>{{ $report['daftar_pengarang'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="mt-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                      <h5 class="card-title text-white">Total Kategori</h5>
                      <h3 class="text-white">{{ $totalKategori }}</h3>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-success text-white">
                    <div class="card-body py-3">
                      <h5 class="card-title text-white">Total Buku</h5>
                      <h3 class="text-white">{{ $totalBuku }}</h3>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-warning text-white">
                    <div class="card-body py-3">
                      <h5 class="card-title text-white">Rata-rata Buku/Kategori</h5>
                      <h3 class="text-white">{{ $totalKategori > 0 ? round($totalBuku / $totalKategori, 1) : 0 }}</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
