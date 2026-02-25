@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="page-header">
      <h3 class="page-title">Tabel Kategori</h3>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Tables</a></li>
          <li class="breadcrumb-item active" aria-current="page">Kategori</li>
        </ol>
      </nav>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            {{-- FORM --}}
            <h4 class="card-title">Tambah Kategori</h4>

            <form method="POST" action="{{ url('/kategori') }}">
              @csrf

              <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" required />
              </div>

              <button type="submit" class="btn btn-primary">Simpan</button>
            </form>

            <hr />

            {{-- ALERT --}}
            @if (session('status'))
              <div class="alert alert-success mt-3">
                {{ session('status') }}
              </div>
            @endif

            {{-- TABLE --}}
            <h4 class="card-title mt-4">Daftar Kategori</h4>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kategori</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_kategori }}</td>
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
