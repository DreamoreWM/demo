@extends('layouts.app')

@section('content')
    <h1>Liste des absences</h1>
    <a href="{{ route('absences.create') }}">Ajouter une absence</a>
    <ul>
        @foreach($absences as $absence)
            <li>
                {{ $absence->start_time }} - {{ $absence->end_time }} ({{ $absence->employee->name }})
                <a href="{{ route('absences.edit', $absence) }}">Modifier</a>
                <form method="POST" action="{{ route('absences.destroy', $absence) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Supprimer</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
