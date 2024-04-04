<form action="{{ route('employees.schedule.store', $employee->id) }}" method="POST">
    @csrf

    @foreach (range(1, 7) as $dayOfWeek)
        @php
            $schedule = $schedules->firstWhere('day_of_week', $dayOfWeek);
            $start_time = $schedule ? (new DateTime($schedule->start_time))->format('H:i') : '';
            $end_time = $schedule ? (new DateTime($schedule->end_time))->format('H:i') : '';
        @endphp
        <div>
            <label for="start_time_{{ $dayOfWeek }}">Jour {{ $dayOfWeek }} Heure de Début:</label>
            <input type="time" id="start_time_{{ $dayOfWeek }}" name="schedules[{{ $dayOfWeek }}][start_time]" value="{{ $start_time }}">
            <input type="hidden" name="schedules[{{ $dayOfWeek }}][day_of_week]" value="{{ $dayOfWeek }}">

            <label for="end_time_{{ $dayOfWeek }}">Heure de Fin:</label>
            <input type="time" id="end_time_{{ $dayOfWeek }}" name="schedules[{{ $dayOfWeek }}][end_time]" value="{{ $end_time }}">
            @if ($schedule)
                <input type="hidden" name="schedules[{{ $dayOfWeek }}][id]" value="{{ $schedule->id }}">
            @endif
        </div>
    @endforeach

    <button type="submit">Ajouter l'Horaire</button>
</form>
