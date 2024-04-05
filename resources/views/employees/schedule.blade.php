@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestion des horraires') }}
    </h2>
@endsection

@section('content')
    <section class="mt-10">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Ajouter l'Horaire
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <form action="{{ route('employees.schedule.store', $employee->id) }}" method="POST" class="px-4 py-5 sm:p-6">
                        @csrf
                        @php
                            $daysOfWeek = [
                                1 => 'Lundi',
                                2 => 'Mardi',
                                3 => 'Mercredi',
                                4 => 'Jeudi',
                                5 => 'Vendredi',
                                6 => 'Samedi',
                                7 => 'Dimanche',
                            ];
                        @endphp
                        <table class="table-auto w-full">
                            <td class="py-2"></td>
                            <td class="mr-2">Heure de Début:</td>
                            <td class="mr-2">Heure de Fin:</td>
                            <td></td>
                            @foreach ($daysOfWeek as $dayNumber => $dayName)
                                @php
                                    $schedule = $schedules->firstWhere('day_of_week', $dayNumber);
                                    $start_time = $schedule ? (new DateTime($schedule->start_time))->format('H:i') : '';
                                    $end_time = $schedule ? (new DateTime($schedule->end_time))->format('H:i') : '';
                                @endphp
                                <tr class="border-b border-gray-200">
                                    <td class="py-2">{{ $dayName }}</td>
                                    <td>
                                        <input type="time" id="start_time_{{ $dayNumber }}" name="schedules[{{ $dayNumber }}][start_time]" value="{{ $start_time }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </td>
                                    <td>
                                        <input type="time" id="end_time_{{ $dayNumber }}" name="schedules[{{ $dayNumber }}][end_time]" value="{{ $end_time }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </td>
                                    <td>
                                        <input type="hidden" name="schedules[{{ $dayNumber }}][day_of_week]" value="{{ $dayNumber }}">
                                        @if ($schedule)
                                            <input type="hidden" name="schedules[{{ $dayNumber }}][id]" value="{{ $schedule->id }}">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <button type="submit" class="mt-3 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Ajouter l'Horaire</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection


