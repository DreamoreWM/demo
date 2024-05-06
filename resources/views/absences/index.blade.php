@extends('layouts.app')

@section('content')
    <section class="mt-10">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

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
            </div>
        </div>
    </section>

@endsection
