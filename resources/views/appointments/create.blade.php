{{-- Vue Blade pour prendre un rendez-vous --}}
@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800  leading-tight">
        {{ __('Prise de rendez-vous') }}
    </h2>
@endsection

@section('content')
    <livewire:appointment-form />
@endsection

@section('scripts')

    <script>
        const swiperEl = document.querySelector('swiper-container');
        const buttonEl = document.querySelector('button');

        buttonEl.addEventListener('click', () => {
            swiperEl.swiper.slideNext();
        });


    </script>


@endsection
