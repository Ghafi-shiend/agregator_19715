@extends('layouts.app')

@section('content')

<h2>Edit Berita</h2>

<form method="POST" action="{{ route('berita.update', $berita->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" value="{{ $berita->judul }}">
    </div>

    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control">{{ $berita->deskripsi }}</textarea>
    </div>

    <button class="btn btn-primary">Update</button>
</form>

@endsection