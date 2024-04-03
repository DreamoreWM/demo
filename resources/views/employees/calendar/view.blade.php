@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Calendrier') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <livewire:employee-calendar />
    </div>
@endsection
