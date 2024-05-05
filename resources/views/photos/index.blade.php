@extends('layouts.app')

@section('content')
    <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="photo">
        <button type="submit">Upload</button>
    </form>

    @foreach($photos as $photo)
        <img src="{{ Storage::url($photo->path) }}">
    @endforeach
@endsection
