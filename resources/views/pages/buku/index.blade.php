@extends('layouts.app')

@section('title', 'Buku')

@section('content')
  <div class="container">
    <div class="page-header">
      <h3 class="page-title">Tabel Buku</h3>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Tables</a></li>
          <li class="breadcrumb-item active" aria-current="page">Buku</li>
        </ol>
      </nav>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            {{-- FORM TAMBAH --}}
            <h4 class="card-title">Tambah Buku</h4>

            <form method="POST" action="{{ url('/buku') }}">
              @csrf

              <div class="form-group">
                <label>Kategori</label>
                <select name="idkategori" class="form-control">
                  @foreach ($kategori as $k)
                    <option value="{{ $k->idkategori }}">
                      {{ $k->nama_kategori }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Kode</label>
                <input type="text" name="kode" class="form-control" />
              </div>

              <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" class="form-control" />
              </div>

              <div class="form-group">
                <label>Pengarang</label>
                <input type="text" name="pengarang" class="form-control" />
              </div>

              <button type="submit" class="btn btn-primary">Simpan</button>
            </form>

            <hr />

            {{-- TABEL --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Daftar Buku</h4>
              <div>
                <a href="{{ url('/label?type=buku') }}" class="btn btn-info btn-sm mr-1">
                  <i class="mdi mdi-label-outline"></i> Cetak Label T&J
                </a>
                <a href="{{ url('/buku/pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                  <i class="mdi mdi-file-pdf"></i> Download PDF (Portrait)
                </a>
              </div>
            </div>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kategori</th>
                  <th>Kode</th>
                  <th>Judul</th>
                  <th>Pengarang</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($buku as $b)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $b->kategori->nama_kategori }}</td>
                    <td>{{ $b->kode }}</td>
                    <td>{{ $b->judul }}</td>
                    <td>{{ $b->pengarang }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
