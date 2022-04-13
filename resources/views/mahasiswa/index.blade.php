@extends('mahasiswa.layout')

@section('content')

<style type="text/css">
		.pagination li{
			float: left;
			list-style-type: none;
			margin:5px;
		}
	</style>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mt-2">
                <h2>JURUSAN TEKNOLOGI INFORMASI-POLITEKNIK NEGERI MALANG</h2>
        </div>
        <div class="float-right my-2">
            <a class="btn btn-success" href="{{ route('mahasiswa.create') }}"> Input Mahasiswa</a>
        </div>
        </div>
    </div>
    <!-- Start kode untuk form pencarian -->

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
@if ($message = Session::get('error'))
    <div class="alert alert-error">
        <p>{{ $message }}</p>
    </div>
@endif

<form class="form-wrapper cf" action="{{ route('mahasiswa.index') }}">
    <div class="form-group w-100 mb-3">
        <input type="text" name="search" class="searchTerm" id="search" placeholder="Masukkan nama..." value="{{ request('search')}}">
        <button type="submit" class="searchButton">Cari</button>
    </div>
    </form>


<table class="table table-bordered">
    <tr>
        <th>Nim</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Jurusan</th>
        <th>Alamat</th>
        <th>Email</th>
        <th>TanggalLahir</th>
        <th>JenisKelamin</th>
        <th width="280x">Action</th>
    </tr>
    @foreach ($paginate as $mhs)
    <tr>
    <td>{{ $mhs ->nim }}</td>
        <td>{{ $mhs ->nama }}</td>
        <td>{{ $mhs ->Kelas -> nama_kelas}}</td>
        <td>{{ $mhs ->jurusan }}</td>
        <td>{{ $mhs ->alamat }}</td> 
        <td>{{ $mhs ->email }}</td> 
        <td>{{ $mhs ->tanggal_lahir }}</td> 
        <td>{{ $mhs ->jenis_kelamin }}</td> 
        <td>
        <form action="{{ route('mahasiswa.destroy',['mahasiswa'=>$mhs->nim]) }}" method="POST">
            <a class="btn btn-info" href="{{ route('mahasiswa.show',$mhs->nim) }}">Show</a>
            <a class="btn btn-primary" href="{{ route('mahasiswa.edit',$mhs->nim) }}">Edit</a>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
        </td>
    </tr>
    @endforeach
</table>
{{$paginate-> links()}}


@endsection