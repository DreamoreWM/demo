@extends('layouts.app')

@section('styles')
    <link href="{{ asset('vendor/fullcalendar/main.css') }}" rel="stylesheet">

@endsection

@section('content')
    <style>
        .appointment-detail {
            padding-left: 30px;
            padding-top: 15px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .appointment-detail .icon-circle {
            margin-right: 10px;
        }

        .appointment-detail p {
            margin: 0;
        }

        .container-card {
            width: 450px;
            height: 330px;
            position: absolute;
            background-color: white;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3), 0 4px 6px rgba(0, 0, 0, 0.2); /* Augmenté pour plus d'ombre */
            border-radius: 10px; /* Réduit pour que l'ombre apparaisse correctement */
            z-index: 999; /* Assure que le container-card soit au-dessus du calendrier */
            display: none;
            opacity: 0;
        }

        /*.container-card::before {*/
        /*    content: "";*/
        /*    position: absolute;*/
        /*    top: 50%;*/
        /*    transform: translateY(-50%);*/
        /*    right: 100%;*/
        /*    width: 0;*/
        /*    height: 0;*/
        /*    border-top: 10px solid transparent;*/
        /*    border-right: 20px solid white;*/
        /*    border-bottom: 10px solid transparent;*/
        /*}*/

        .container-card.slide-in {
            animation: slideInFromRight 0.2s forwards;
        }

        .top {
            width: 100%;
            height: 140px;
            background-image: url('/images/img.png');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-start;
            box-sizing: border-box;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            overflow: hidden;
        }

        .icons {
            display: flex;
            align-items: center;
            margin-left: auto;
            padding-right: 10px;
            padding-top: 3px;
        }

        .tool{
            display: flex;
            padding-right: 15px;
        }

        .close{
            width: 45px;
            height: 45px;
        }

        .outer-circle {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .inner-circle {
            width: 32px;
            height: 32px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle {
            position: relative;
            width: 35px;
            height: 35px;
            margin: 3px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle-close {
            position: relative;
            width: 40px;
            height: 40px;
            margin: 3px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-bottom{
            display: flex;
            align-items: center;
            padding-right: 10px;
        }

        .icon-background {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icons img {
            width: 40px;
            height: 40px;
            margin-left: 10px;
        }

        .bottom {
            width: 100%;
            height: 190px;
            padding-top: 15px;
            background-color: #fff;
            box-sizing: border-box;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            overflow: hidden;
        }
    </style>
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
                                        <input class="form-check-input prestationFilter" type="checkbox" value="{{ $prestation->id }}" id="prestation{{ $prestation->id }}" data-name={{ $prestation->nom }} data-id={{ $prestation->id }} data-duree="{{ $prestation->temps }}">
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

            <div class="container-card" id="appointment-card" style="display: none; padding: 0">
                <div class="top">
                    <div class="icons">
                        <div class="tool">
                            <div class="icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20px" height="20px" viewBox="0,0,256,256">
                                    <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(8.53333,8.53333)"><path d="M22.82813,3c-0.51175,0 -1.02356,0.19544 -1.41406,0.58594l-2.41406,2.41406l5,5l2.41406,-2.41406c0.781,-0.781 0.781,-2.04713 0,-2.82812l-2.17187,-2.17187c-0.3905,-0.3905 -0.90231,-0.58594 -1.41406,-0.58594zM17,8l-11.74023,11.74023c0,0 0.91777,-0.08223 1.25977,0.25977c0.342,0.342 0.06047,2.58 0.48047,3c0.42,0.42 2.64389,0.12436 2.96289,0.44336c0.319,0.319 0.29688,1.29688 0.29688,1.29688l11.74023,-11.74023zM4,23l-0.94336,2.67188c-0.03709,0.10544 -0.05623,0.21635 -0.05664,0.32813c0,0.55228 0.44772,1 1,1c0.11177,-0.00041 0.22268,-0.01956 0.32813,-0.05664c0.00326,-0.00128 0.00652,-0.00259 0.00977,-0.00391l0.02539,-0.00781c0.00196,-0.0013 0.00391,-0.0026 0.00586,-0.00391l2.63086,-0.92773l-1.5,-1.5z"></path></g></g>
                                </svg>
                            </div>
                            <div class="icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20px" height="2Opx" viewBox="0,0,256,256">
                                    <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(10.66667,10.66667)"><path d="M10,2l-1,1h-5v2h1v15c0,0.52222 0.19133,1.05461 0.56836,1.43164c0.37703,0.37703 0.90942,0.56836 1.43164,0.56836h10c0.52222,0 1.05461,-0.19133 1.43164,-0.56836c0.37703,-0.37703 0.56836,-0.90942 0.56836,-1.43164v-15h1v-2h-5l-1,-1zM7,5h10v15h-10zM9,7v11h2v-11zM13,7v11h2v-11z"></path></g></g>
                                </svg>
                            </div>
                        </div>
                        <div class="close">
                            <div class="icon-circle-close">
                                <div class="outer-circle">
                                    <div class="inner-circle">
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="16px" height="16px" viewBox="0,0,256,256">
                                            <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M9.15625,6.3125l-2.84375,2.84375l15.84375,15.84375l-15.9375,15.96875l2.8125,2.8125l15.96875,-15.9375l15.9375,15.9375l2.84375,-2.84375l-15.9375,-15.9375l15.84375,-15.84375l-2.84375,-2.84375l-15.84375,15.84375z"></path></g></g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bottom">
                    <div class="appointment-detail">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" viewBox="0 0 24 24" class=" NMm5M"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-.8 2L12 10.8 4.8 6h14.4zM4 18V7.87l8 5.33 8-5.33V18H4z"></path></svg>
                            </div>
                        </div>
                        <p>Jeudi, 18 avril • 9:00 à 9:40am</p>
                    </div>
                    <div class="appointment-detail">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" viewBox="0 0 24 24" class=" NMm5M"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-.8 2L12 10.8 4.8 6h14.4zM4 18V7.87l8 5.33 8-5.33V18H4z"></path></svg>
                            </div>
                        </div>
                        <p>30 minutes avant, par e-mail</p>
                    </div>
                    <div class="appointment-detail">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" viewBox="0 0 24 24" class=" NMm5M"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-.8 2L12 10.8 4.8 6h14.4zM4 18V7.87l8 5.33 8-5.33V18H4z"></path></svg>
                            </div>
                        </div>
                        <p>Alexandre Idziak</p>
                    </div>
                    <p id="appointmentTitle"></p>
                    <p id="appointmentStart"></p>
                    <p id="appointmentEmployee"></p>
                    <p id="appointmentClientName"></p>
                    <p id="appointmentClientEmail"></p>
                    <p id="appointmentPrestations"></p>
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
                        <input type="hidden" name="employeeId" id="employeeId">
                        <input type="hidden" name="selectedPrestationsInfos" id="selectedPrestationsInfos">
                        <input type="hidden" name="eventStart" id="eventStart">
                        <input type="hidden" name="totalDuration" id="totalDuration">
                        <div class="form-group">
                            <label for="userId">Choisir un Client</label>
                            <select name="user_id" id="userId" class="form-control">
                                <option value="">Sélectionnez un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ 'user-'.$user->id }}">{{ $user->name }} (User)</option>
                                @endforeach
                                @foreach($temporaryUsers as $temporaryUser)
                                    <option value="{{ 'temporary-'.$temporaryUser->id }}">{{ $temporaryUser->name }} (Temporary User)</option>
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
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('appointment-card').style.display = 'none';
        });

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
                    console.log(info.event.start);

                    const start = info.event.start;
                    const startTime = start.toISOString().slice(0, 19).replace('T', ' ');

                    console.log(startTime);

                    console.log(info.event);

                   if(info.event.extendedProps.reserved === true) {
                       const appointmentCard = document.getElementById('appointment-card');

                       var appointmentDetailElements = document.querySelectorAll('.appointment-detail p');

                       // Mettre à jour les éléments de la carte avec les informations de l'événement
                       appointmentDetailElements[0].textContent = info.event.title; // Mettre à jour le titre
                       appointmentDetailElements[1].textContent = info.event.start.toISOString(); // Mettre à jour la date de début
                       appointmentDetailElements[2].textContent = info.event.extendedProps.employee.name; // Mettre à jour le nom de l'employé

                       // Positionner le container-card à gauche de l'événement
                       const eventRect = info.el.getBoundingClientRect();
                       const initialLeft = info.jsEvent.pageX - 530;
                       appointmentCard.style.left = `${initialLeft}px`;

                       appointmentCard.style.top = `${info.jsEvent.pageY - 315}px`;

                       // Retirer la classe 'slide-in' avant d'ajouter la nouvelle règle d'animation
                       appointmentCard.classList.remove('slide-in');

                       // Supprimer la règle d'animation précédente
                       const styleSheet = document.styleSheets[0];
                       const previousRuleIndex = styleSheet.cssRules.length - 1;
                       if (styleSheet.cssRules[previousRuleIndex].name === 'slideInFromRight') {
                           styleSheet.deleteRule(previousRuleIndex);
                       }

                       // Mettre à jour l'animation pour qu'elle commence à la position de l'événement cliqué et se termine légèrement à gauche
                       const keyframes = `@keyframes slideInFromRight {
            0% {
                left: ${initialLeft}px;
                opacity: 0;
            }
            100% {
                left: ${initialLeft - 30}px;
                opacity: 1;
            }
        }`;
                       styleSheet.insertRule(keyframes, styleSheet.cssRules.length);

                       // Ajouter la classe 'slide-in' pour déclencher l'animation dans une nouvelle boucle d'événements du navigateur
                       setTimeout(function() {
                           appointmentCard.classList.add('slide-in');
                       }, 0);

                       // Afficher le container-card
                       appointmentCard.style.display = 'block';
                   }
                    document.getElementById('employeeId').value = info.event._def.extendedProps.employee.id;
                    document.getElementById('eventStart').value = startTime;
                    if(info.event.extendedProps.reserved === false && document.getElementById('selectedPrestationsInfos').value) {
                        $('#appointmentModal').modal('show');
                    }
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

                const selectedPrestationsInfos = JSON.stringify(
                    Array.from(prestationFilters)
                        .filter(input => input.checked)
                        .map(input => ({
                            duree: parseInt(input.dataset.duree || 0),
                            name: input.dataset.name,
                            id: parseInt(input.dataset.id)
                        }))
                );

                document.getElementById('selectedPrestationsInfos').value = selectedPrestationsInfos

                console.log(selectedPrestationsInfos);

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

                console.log(totalPrestationDurationMinutes);
                console.log(filteredByEmployee);

                document.getElementById('totalDuration').value = totalPrestationDurationMinutes;

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
