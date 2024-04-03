@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Gestion des Créneaux pour l'employé : {{ $employee->name }}
</h2>
@endsection

@section('content')
<div class="container py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
        $firstDayOfMonth = now()->startOfMonth();
        $weeksInMonth = $firstDayOfMonth->copy()->endOfMonth()->weekOfYear - $firstDayOfMonth->weekOfYear + 1;
        @endphp
        @for ($week = 1; $week <= $weeksInMonth; $week++)
        @php
        $firstDayOfWeek = $firstDayOfMonth->copy()->addWeeks($week - 1)->startOfWeek();
        @endphp
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('employees.slots.update', $employee) }}">
                            @csrf
                            @method('PUT')
                            <table class="table" id="week-{{ $week }}">
                                <thead>
                                <tr>
                                    <th>Jour / Heure</th>
                                    @for ($hour = 8; $hour < 17; $hour++)
                                    <th>{{ $hour }}h - {{ $hour + 1 }}h</th>
                                    @endfor
                                    <th><input type="checkbox" onclick="toggleAll(this, 'week-{{ $week }}')"> Tout Cocher</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for ($day = 0; $day < 7; $day++)
                                @php
                                $currentDay = $firstDayOfWeek->copy()->addDays($day);
                                @endphp
                                <tr>
                                    <td>{{ $currentDay->format('l j F') }}</td>
                                    @for ($hour = 8; $hour < 17; $hour++)
                                    @php
                                    $dayOfWeek = strtolower($currentDay->format('l'));
                                    $slot = $employee->slots()->where('day_of_week', $dayOfWeek)
                                    ->where('date', $currentDay->format('Y-m-d'))
                                    ->where('start_time', sprintf('%02d:00:00', $hour))
                                    ->first();
                                    $checked = $slot ? 'checked' : '';
                                    $checkboxId = "slot-{$dayOfWeek}-{$currentDay->format('Y-m-d')}-{$hour}";
                                    @endphp
                                    <td>
                                        <input type="checkbox" id="{{ $checkboxId }}" name="slots[{{ $dayOfWeek }}][{{ $currentDay->format('Y-m-d') }}][]" value="{{ $hour }}" {{ $checked ? 'checked' : '' }}>

                                    </td>
                                    @endfor
                                    <td><input type="checkbox" onclick="toggleRow(this)"> Cocher Jour</td>
                                </tr>
                                @endfor
                                </tbody>
                            </table>
                            <div class="text-center">
                                <button class="btn btn-primary">Enregistrer les Créneaux</button>
                                <button class="btn btn-warning" onclick="window.location='{{ route('employees.index') }}'">Retour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

<script>
    function toggleRow(checkbox) {
        const cells = checkbox.closest('tr').querySelectorAll('input[type=checkbox]');
        cells.forEach(cell => {
            cell.checked = checkbox.checked;
        });
    }

    function toggleAll(globalCheckbox, tableId) {
        const table = document.getElementById(tableId);
        const checkboxes = table.querySelectorAll('input[type=checkbox]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = globalCheckbox.checked;
        });
    }
</script>
@endsection


@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Gestion des Créneaux pour l'employé : {{ $employee->name }}
</h2>
@endsection

@section('content')
<div class="container py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
        $firstDayOfMonth = now()->startOfMonth();
        $weeksInMonth = $firstDayOfMonth->copy()->endOfMonth()->weekOfYear - $firstDayOfMonth->weekOfYear + 1;
        @endphp
        @for ($week = 1; $week <= $weeksInMonth; $week++)
        @php
        $firstDayOfWeek = $firstDayOfMonth->copy()->addWeeks($week - 1)->startOfWeek();
        @endphp
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('employees.slots.update', $employee) }}">
                            @csrf
                            @method('PUT')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Jour / Heure</th>
                                    @for ($hour = 8; $hour < 17; $hour++)
                                    <th>{{ $hour }}h - {{ $hour + 1 }}h</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                @for ($day = 0; $day < 7; $day++)
                                @php
                                $currentDay = $firstDayOfWeek->copy()->addDays($day);
                                @endphp
                                <tr>
                                    <td>{{ $currentDay->format('l j F') }}</td>
                                    @for ($hour = 8; $hour < 17; $hour++)
                                    @php
                                    $dayOfWeek = strtolower($currentDay->format('l'));
                                    $slot = $employee->slots()->where('day_of_week', $dayOfWeek)
                                    ->where('date', $currentDay->format('Y-m-d'))
                                    ->where('start_time', sprintf('%02d:00:00', $hour))
                                    ->first();
                                    $checked = $slot ? 'checked' : '';
                                    $checkboxId = "slot-{$dayOfWeek}-{$currentDay->format('Y-m-d')}-{$hour}";
                                    @endphp
                                    <td>
                                        <input type="checkbox" id="{{ $checkboxId }}" name="slots[{{ $dayOfWeek }}][{{ $currentDay->format('Y-m-d') }}][]" value="{{ $hour }}" {{ $checked }}>
                                    </td>
                                    @endfor
                                </tr>
                                @endfor
                                </tbody>
                            </table>
                            <div class="text-center">
                                <button class="btn btn-primary">Enregistrer les Créneaux</button>
                                <button class="btn btn-warning" onclick="window.location='{{ route('employees.index') }}'">Retour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection




