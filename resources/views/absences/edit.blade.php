@extends('layouts.app')

@section('content')
    <h1>Modifier une absence</h1>
    <form method="POST" action="{{ route('absences.update', $absence) }}">
        @csrf
        @method('PUT')
        <label for="employee_id">Employé :</label>
        <select id="employee_id" name="employee_id" required>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ $employee->id == $absence->employee_id ? 'selected' : '' }}>{{ $employee->name }}</option>
            @endforeach
        </select>
        <label for="start_time">Date de début :</label>
        <input type="datetime-local" id="start_time" name="start_time" value="{{ $absence->start_time }}" required>
        <label for="end_time">Date de fin :</label>
        <input type="datetime-local" id="end_time" name="end_time" value="{{ $absence->end_time }}" required>
        <button type="submit">Modifier</button>
    </form>
@endsection
