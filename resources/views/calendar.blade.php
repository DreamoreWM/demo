@extends('layouts.app')

@section('styles')
    <link href="{{ asset('vendor/fullcalendar/main.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid pt-3">
        <div class="row">
            <!-- Sidebar pour les filtres avec fond blanc et espace interne -->
            <!-- Sidebar pour les filtres avec fond blanc, bords arrondis, et espace interne -->
            <div class="col-md-3">

                <div style="margin-bottom: 20px;" class="bg-white rounded shadow p-3">
                    <label>Filtrer par employé :</label>
                    @foreach($employees as $employee)
                        <div class="form-check">
                            <input class="form-check-input employeeFilter" type="checkbox" value="{{ $employee->id }}" id="employee{{ $employee->id }}">
                            <label class="form-check-label" for="employee{{ $employee->id }}">
                                <span style="display: inline-block; width: 10px; height: 10px; background-color: {{ $employee->color }}; margin-right: 5px;"></span>
                                {{ $employee->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchPrestation" placeholder="Rechercher des prestations..." aria-label="Rechercher des prestations">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <!-- Container pour les prestations avec scroll, fond blanc, et bords arrondis -->
                <div id="prestation" class="bg-white rounded shadow p-3" style="max-height: 600px; overflow-y: auto;">
                    <!-- Avant la liste des prestations -->


                    <div id="prestationsCards" class="d-flex flex-column">
                        @foreach($prestations as $prestation)
                            <div class="card" style="margin-bottom: 10px;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $prestation->nom }}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{ $prestation->temps }} minutes</h6>
                                    <div class="form-check">
                                        <input class="form-check-input prestationFilter" type="checkbox" value="{{ $prestation->id }}" id="prestation{{ $prestation->id }}" data-duree="{{ $prestation->temps }}">
                                        <label class="form-check-label" for="prestation{{ $prestation->id }}">
                                            Sélectionner
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>


            <!-- Contenu principal : Calendrier -->
            <div class="col-md-9" >
                <div class="card">
                    <div class="card-header">{{ __('Calendar') }}</div>
                    <div class="card-body">
                        <div id="calendar" style="max-height: 750px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal Structure (exemple avec Bootstrap) -->
    <div class="modal" id="appointmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('calendar.assign') }}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Attribuer Créneau</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="slot_id" id="slotId">
                        <div class="form-group">
                            <label for="userId">Choisir un Client</label>
                            <select name="user_id" id="userId" class="form-control">
                                <option value="">Sélectionnez un utilisateur</option>
                                @foreach(App\Models\User::all() as $user)
                                    <option value="{{ get_class($user) === 'App\Models\User' ? 'user-'.$user->id : 'tempuser-'.$user->id }}">
                                        {{ $user->name }} ({{ get_class($user) === 'App\Models\User' ? 'User' : 'Temporary User' }})
                                    </option>
                                @endforeach
                                <option value="new">Ajouter un nouvel utilisateur</option>
                            </select>
                        </div>

                        <!-- Nouvelle partie pour ajouter un utilisateur si "Ajouter un nouvel utilisateur" est sélectionné -->
                        <div id="newUserFields" style="display:none;">
                            <div class="form-group">
                                <label for="userName">Nom</label>
                                <input type="text" name="user_name" id="userName" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="userEmail">Email</label>
                                <input type="email" name="user_email" id="userEmail" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Attribuer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        var events = @json($events);

    </script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.getElementById('userId').addEventListener('change', function() {
            if (this.value === 'new') {
                document.getElementById('newUserFields').style.display = '';
            } else {
                document.getElementById('newUserFields').style.display = 'none';
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let closeButton = document.querySelector('.modal .close');

            closeButton.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche la soumission du formulaire
                // Ferme le modal. Vous aurez besoin de la logique spécifique à votre mise en œuvre de modal si vous n'utilisez pas Bootstrap
                let modal = document.getElementById('appointmentModal');
                modal.style.display = 'none';
            });
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                slotLabelInterval: '01:00',
                eventClick: function(info) {
                    document.getElementById('slotId').value = info.event.id;
                    $('#appointmentModal').modal('show');
                    document.querySelector('.close').addEventListener('click', function() {
                        $('#appointmentModal').modal('hide');
                    });
                },
                eventContent: function(arg) {
                    // Retourne un élément HTML ou un objet pour l'affichage de l'événement.
                    // Ici, on retourne simplement le titre sans l'heure.
                    return {
                        html: `<div style="height:10px; width:100%; background-color:${arg.event.extendedProps.employee.color};"></div><div style="padding-top:10px;">${arg.event.title}</div>`
                    };
                },
                events: events
            });
            calendar.render();

            const employeeFilters = document.querySelectorAll('.employeeFilter');

            employeeFilters.forEach(filter => {
                filter.addEventListener('change', updateCalendarEvents);
            });

            const prestationFilters = document.querySelectorAll('.prestationFilter');
            prestationFilters.forEach(filter => {
                filter.addEventListener('change', updateCalendarEvents);
            });

            function updateCalendarEvents() {
                const selectedEmployeeIds = Array.from(employeeFilters)
                    .filter(input => input.checked)
                    .map(input => parseInt(input.value));

                // Modifier ici pour les cases à cocher des prestations
                const selectedPrestations = Array.from(prestationFilters)
                    .filter(input => input.checked)
                    .map(input => parseInt(input.dataset.duree || 0));

                // Calculer la durée totale des prestations sélectionnées
                const totalPrestationDurationMinutes = selectedPrestations.reduce((total, current) => total + current, 0);
                const slotsNeeded = Math.ceil(totalPrestationDurationMinutes / 60); // Chaque créneau dure 60 minutes

                let availableSlots = [];

                // Filtrer les événements par employé sélectionné
                let filteredByEmployee = events.filter(event =>
                    selectedEmployeeIds.length === 0 || selectedEmployeeIds.includes(event.employee.id)
                );

                // Vérifier chaque créneau pour voir s'il démarre une série de créneaux consécutifs suffisants
                for (let i = 0; i < filteredByEmployee.length; i++) {
                    let series = [filteredByEmployee[i]]; // Commencer une nouvelle série avec le créneau actuel
                    let seriesEnd = new Date(filteredByEmployee[i].end).getTime();

                    for (let j = i + 1; j < filteredByEmployee.length && series.length < slotsNeeded; j++) {
                        let nextStart = new Date(filteredByEmployee[j].start).getTime();
                        let nextEnd = new Date(filteredByEmployee[j].end).getTime();

                        // Vérifier si le créneau suivant est consécutif et ajouter à la série
                        if (seriesEnd === nextStart) {
                            series.push(filteredByEmployee[j]);
                            seriesEnd = nextEnd;
                        }
                    }

                    // Si la série de créneaux est suffisante pour la prestation, ajouter le premier créneau de la série
                    if (series.length >= slotsNeeded) {
                        availableSlots.push(filteredByEmployee[i]);
                        // Ne pas sauter les créneaux déjà couverts car ils peuvent démarrer une nouvelle série valide
                    }
                }

                // Mettre à jour le calendrier avec les créneaux disponibles
                calendar.removeAllEvents();
                calendar.addEventSource(availableSlots);
                calendar.render();
            }

            const searchPrestationInput = document.getElementById('searchPrestation');
            searchPrestationInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const prestations = document.querySelectorAll('#prestationsCards .card');

                prestations.forEach(function(prestation) {
                    const title = prestation.querySelector('.card-title').textContent.toLowerCase();
                    if(title.includes(searchTerm)) {
                        prestation.style.display = '';
                    } else {
                        prestation.style.display = 'none';
                    }
                });
            });

        });
    </script>
@endpush
