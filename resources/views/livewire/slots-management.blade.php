<div>
    <div class="container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('employees.slots.update', $employee->id) }}">
                @csrf
                @method('PUT')

                <label for="day_of_week">Jour de la semaine :</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="1">Lundi</option>
                    <option value="2">Mardi</option>
                    <option value="3">Mercredi</option>
                    <option value="4">Jeudi</option>
                    <option value="5">Vendredi</option>
                    <option value="6">Samedi</option>
                    <option value="7">Dimanche</option>
                </select>

                <label for="start_time">Heure de début :</label>
                <input type="time" id="start_time" name="start_time" required>

                <label for="end_time">Heure de fin :</label>
                <input type="time" id="end_time" name="end_time" required>

                <button type="submit">Enregistrer les créneaux</button>
            </form>



            {{--            @for ($week = 1; $week <= $weeksInMonth; $week++)--}}
{{--                @php--}}
{{--                    $firstDayOfWeek = $firstDayOfMonth->copy()->addWeeks($week - 1)->startOfWeek();--}}
{{--                @endphp--}}
{{--                <div class="row justify-content-center">--}}
{{--                    <div class="col-md-12">--}}
{{--                        <div class="card bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">--}}
{{--                            <div class="card-body">--}}
{{--                                <form method="POST" action="{{ route('employees.slots.update', $employee) }}">--}}
{{--                                    @csrf--}}
{{--                                    @method('PUT')--}}
{{--                                    <table class="table" id="week-{{ $week }}">--}}
{{--                                        <thead>--}}
{{--                                        <tr>--}}
{{--                                            <th>Jour / Heure</th>--}}
{{--                                            @for ($hour = 8; $hour < 17; $hour++)--}}
{{--                                                <th>{{ $hour }}h - {{ $hour + 1 }}h</th>--}}
{{--                                            @endfor--}}
{{--                                            <th><input type="checkbox" onclick="toggleAll(this, 'week-{{ $week }}')"> Tout Cocher</th>--}}
{{--                                        </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody>--}}
{{--                                        @for ($day = 0; $day < 7; $day++)--}}
{{--                                            @php--}}
{{--                                                $currentDay = $firstDayOfWeek->copy()->addDays($day);--}}
{{--                                            @endphp--}}
{{--                                            <tr>--}}
{{--                                                <td>{{ $currentDay->format('l j F') }}</td>--}}
{{--                                                @for ($hour = 8; $hour < 17; $hour++)--}}
{{--                                                    @php--}}
{{--                                                        $dayOfWeek = strtolower($currentDay->format('l'));--}}
{{--                                                        $slot = $employee->slots()->where('day_of_week', $dayOfWeek)--}}
{{--                                                        ->where('date', $currentDay->format('Y-m-d'))--}}
{{--                                                        ->where('start_time', sprintf('%02d:00:00', $hour))--}}
{{--                                                        ->first();--}}
{{--                                                        $checked = $slot ? 'checked' : '';--}}
{{--                                                        $checkboxId = "slot-{$dayOfWeek}-{$currentDay->format('Y-m-d')}-{$hour}";--}}
{{--                                                    @endphp--}}
{{--                                                    <td>--}}
{{--                                                        <input type="checkbox" id="{{ $checkboxId }}" name="slots[{{ $dayOfWeek }}][{{ $currentDay->format('Y-m-d') }}][]" value="{{ $hour }}" {{ $checked ? 'checked' : '' }}>--}}

{{--                                                    </td>--}}
{{--                                                @endfor--}}
{{--                                                <td><input type="checkbox" onclick="toggleRow(this)"> Cocher Jour</td>--}}
{{--                                            </tr>--}}
{{--                                        @endfor--}}
{{--                                        </tbody>--}}
{{--                                        </table>--}}


{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endfor--}}
{{--            <div class="text-center">--}}
{{--                <button class="btn btn-primary"  wire.live:click="confirmItemDeletion()">Enregistrer les Créneaux</button>--}}
{{--                <button class="btn btn-warning" onclick="window.location='{{ route('employees.index') }}'">Retour</button>--}}
{{--            </div>--}}
{{--            </form>--}}
        </div>
    </div>

{{--    <div class="modal fade {{ $confirmingItemDeletion ? 'show' : '' }}" style="display: {{ $confirmingItemDeletion ? 'block' : 'none' }};" tabindex="-1" role="dialog">--}}
{{--        <div class="modal-dialog" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Confirmation Requise</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">×</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    Êtes-vous sûr de vouloir effectuer cette action ?--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn-secondary" wire:click="$set('confirmingItemDeletion', false)">Annuler</button>--}}
{{--                    <button type="button" class="btn btn-primary" wire:click="bookSlot">Confirmer</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

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
    </script>{-- If your happiness depends on money, you will never be happy with yourself. --}}
</div>
