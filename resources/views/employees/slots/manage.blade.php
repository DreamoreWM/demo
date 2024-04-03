@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Créneaux pour l'employé : {{ $employee->name }}
    </h2>
@endsection

@section('content')
    @livewire('slots-management', ['employee' => $employee])
@endsection



