@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/templatemo-style.css">

    <section class="mt-10">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photo">
                    <button type="submit">Upload</button>
                </form>

                <div class="portfolio" data-aos="fade-right" data-aos-offset="150">
                    <div class="container-fluid tm-container-content" >
                        <div class="row tm-gallery pt-5" style="justify-content: center !important">
                            @foreach($photos as $photo)
                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5"  data-aos="fade-up" data-aos-offset="250">
                                    <figure class="effect-ming tm-video-item">
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="Image" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                        <figcaption class="d-flex align-items-center justify-content-center">
                                            <h2>Image</h2>
                                            <a href="photo-detail.html">View more</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            @endforeach
                        </div> <!-- row -->
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
