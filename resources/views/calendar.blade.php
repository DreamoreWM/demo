@extends('layouts.app')

@section('styles')
    <link href="{{ asset('vendor/fullcalendar/main.css') }}" rel="stylesheet">

@endsection

@section('content')
    <style>
        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .appointment-detail {
            padding-left: 30px;
            padding-top: 15px;
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
            max-height: 1000px;
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
            max-height: 1000px;
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
                <div id="notification" style="display: none; color: white; background-color: red; text-align: center; padding: 10px;">
                    Veuillez sélectionner une prestation avant de cliquer sur un rendez-vous libre.
                </div>
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
                            <div class="icon-circle icon-trash">
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
                                <svg focusable="false" width="20" height="20" viewBox="-0.5 0 15 15" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="#000000" fill-rule="evenodd" d="M107,154.006845 C107,153.45078 107.449949,153 108.006845,153 L119.993155,153 C120.54922,153 121,153.449949 121,154.006845 L121,165.993155 C121,166.54922 120.550051,167 119.993155,167 L108.006845,167 C107.45078,167 107,166.550051 107,165.993155 L107,154.006845 Z M108,157 L120,157 L120,166 L108,166 L108,157 Z M116.5,163.5 L116.5,159.5 L115.757485,159.5 L114.5,160.765367 L114.98503,161.275112 L115.649701,160.597451 L115.649701,163.5 L116.5,163.5 Z M112.5,163.5 C113.412548,163.5 114,163.029753 114,162.362119 C114,161.781567 113.498099,161.473875 113.110266,161.433237 C113.532319,161.357765 113.942966,161.038462 113.942966,160.550798 C113.942966,159.906386 113.395437,159.5 112.505703,159.5 C111.838403,159.5 111.359316,159.761248 111.051331,160.115385 L111.456274,160.632075 C111.724335,160.370827 112.055133,160.231495 112.425856,160.231495 C112.819392,160.231495 113.13308,160.382438 113.13308,160.690131 C113.13308,160.974601 112.847909,161.102322 112.425856,161.102322 C112.28327,161.102322 112.020913,161.102322 111.952471,161.096517 L111.952471,161.839623 C112.009506,161.833817 112.26616,161.828012 112.425856,161.828012 C112.956274,161.828012 113.190114,161.967344 113.190114,162.275036 C113.190114,162.565312 112.93346,162.768505 112.471483,162.768505 C112.10076,162.768505 111.684411,162.605951 111.427757,162.327286 L111,162.87881 C111.279468,163.227141 111.804183,163.5 112.5,163.5 Z M110,152.5 C110,152.223858 110.214035,152 110.504684,152 L111.495316,152 C111.774045,152 112,152.231934 112,152.5 L112,153 L110,153 L110,152.5 Z M116,152.5 C116,152.223858 116.214035,152 116.504684,152 L117.495316,152 C117.774045,152 118,152.231934 118,152.5 L118,153 L116,153 L116,152.5 Z" transform="translate(-107 -152)"></path> </g></svg>
                            </div>
                        </div>
                        <p></p>
                    </div>
                    <div class="appointment-detail">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#000000;} </style> <g> <path class="st0" d="M256.008,411.524c54.5,0,91.968-7.079,92.54-13.881c2.373-28.421-34.508-43.262-49.381-48.834 c-7.976-2.984-19.588-11.69-19.588-17.103c0-3.587,0-8.071,0-14.214c4.611-5.119,8.095-15.532,10.183-27.317 c4.857-1.738,7.627-4.524,11.095-16.65c3.69-12.93-5.548-12.5-5.548-12.5c7.468-24.715-2.357-47.944-18.825-46.246 c-11.358-19.857-49.397,4.54-61.31,2.841c0,6.818,2.834,11.92,2.834,11.92c-4.143,7.882-2.548,23.564-1.389,31.485 c-0.667,0-9.016,0.079-5.468,12.5c3.452,12.126,6.23,14.912,11.088,16.65c2.079,11.786,5.571,22.198,10.198,27.317 c0,6.143,0,10.627,0,14.214c0,5.413-12.35,14.548-19.611,17.103c-14.953,5.262-51.746,20.413-49.373,48.834 C164.024,404.444,201.491,411.524,256.008,411.524z"></path> <path class="st0" d="M404.976,56.889h-75.833v16.254c0,31.365-25.524,56.889-56.889,56.889h-32.508 c-31.366,0-56.889-25.524-56.889-56.889V56.889h-75.834c-25.444,0-46.071,20.627-46.071,46.071v362.969 c0,25.444,20.627,46.071,46.071,46.071h297.952c25.445,0,46.072-20.627,46.072-46.071V102.96 C451.048,77.516,430.421,56.889,404.976,56.889z M402.286,463.238H109.714V150.349h292.572V463.238z"></path> <path class="st0" d="M239.746,113.778h32.508c22.405,0,40.635-18.23,40.635-40.635V40.635C312.889,18.23,294.659,0,272.254,0 h-32.508c-22.406,0-40.635,18.23-40.635,40.635v32.508C199.111,95.547,217.341,113.778,239.746,113.778z M231.619,40.635 c0-4.492,3.634-8.127,8.127-8.127h32.508c4.492,0,8.127,3.635,8.127,8.127v16.254c0,4.492-3.635,8.127-8.127,8.127h-32.508 c-4.493,0-8.127-3.635-8.127-8.127V40.635z"></path> </g> </g></svg>                            </div>
                        </div>
                        <p>Alexandre Idziak</p>
                    </div>
                    <div class="appointment-detail">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 477.297 477.297" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <g> <path d="M42.85,358.075c0-24.138,0-306.758,0-330.917c23.9,0,278.867,0,302.767,0c0,8.542,0,49.44,0,99.722 c5.846-1.079,11.842-1.812,17.99-1.812c3.149,0,6.126,0.647,9.232,0.928V0H15.649v385.233h224.638v-27.158 C158.534,358.075,57.475,358.075,42.85,358.075z"></path> <path d="M81.527,206.842h184.495c1.812-10.16,5.393-19.608,10.095-28.452H81.527V206.842z"></path> <rect x="81.527" y="89.432" width="225.372" height="28.452"></rect> <path d="M81.527,295.822h191.268c5.112-3.106,10.57-5.63,16.415-7.183c-5.544-6.45-10.095-13.697-13.978-21.269H81.527V295.822z"></path> <path d="M363.629,298.669c41.071,0,74.16-33.197,74.16-74.139c0-40.984-33.09-74.16-74.16-74.16 c-40.898,0-74.009,33.176-74.009,74.16C289.62,265.472,322.731,298.669,363.629,298.669z"></path> <path d="M423.143,310.706H304.288c-21.226,0-38.612,19.457-38.612,43.422v119.33c0,1.316,0.604,2.481,0.69,3.84h194.59 c0.086-1.337,0.69-2.524,0.69-3.84v-119.33C461.733,330.227,444.39,310.706,423.143,310.706z"></path> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </g> </g></svg>                            </div>
                        </div>
                        <p>Alexandre Idziak</p>
                    </div>
                    <div class="appointment-detail" style="padding-bottom: 25px">
                        <div class="icon-bottom">
                            <div class="icon-background">
                                <svg focusable="false" width="20" height="20" viewBox="0 0 24 24" class=" NMm5M"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-.8 2L12 10.8 4.8 6h14.4zM4 18V7.87l8 5.33 8-5.33V18H4z"></path></svg>
                            </div>
                        </div>
                        <p></p>
                    </div>
                    <div style="text-align: center">
                        <h1 style="font-size: 20px; font-weight: bold">Prestations à effectuées : </h1>
                    </div>
                    <div class="appointment-detail" style="padding-bottom: 25px">
                        <div class="icon-bottom">
                            <div class="icon-background">
                            </div>
                        </div>
                        <p>Alexandre Idziak</p>
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
                        <input type="hidden" name="employeeId" id="employeeId">
                        <input type="hidden" name="selectedPrestationsInfos" id="selectedPrestationsInfos">
                        <input type="hidden" name="eventStart" id="eventStart">
                        <input type="hidden" name="totalDuration" id="totalDuration">
                        <!-- Ajout du champ pour l'employé sélectionné -->
                        <div class="form-group">
                            <label for="selectedEmployee">Employé sélectionné :</label>
                            <input type="text" id="selectedEmployee" class="form-control" readonly>
                        </div>

                        <!-- Ajout de la div pour la liste des prestations -->
                        <div class="form-group">
                            <label>Prestations à effectuer :</label>
                            <div id="prestationList"></div>
                        </div>

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

        document.body.addEventListener('click', function(event) {
            const appointmentCard = document.getElementById('appointment-card');
            const calendarEvents = document.querySelectorAll('.fc-event'); // Sélectionne tous les événements de calendrier

            // Vérifie si le clic a été effectué sur l'un des événements
            const isClickInsideEvent = Array.from(calendarEvents).some(calendarEvent => calendarEvent.contains(event.target));

            if (!appointmentCard.contains(event.target) && !isClickInsideEvent && appointmentCard.style.display !== 'none') {
                appointmentCard.style.display = 'none';
            }
        });

        // Ajouter un gestionnaire d'événements sur le bouton de la poubelle
        // Ajouter un gestionnaire d'événements sur le bouton de la poubelle

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

                    const selectedEmployee = document.getElementById('selectedEmployee');
                    selectedEmployee.value = info.event._def.extendedProps.employee.name;

                    // Mettre à jour la liste des prestations
                    const prestationList = document.getElementById('prestationList');
                    const selectedPrestationsInfos = document.getElementById('selectedPrestationsInfos').value;
                    const notification = document.getElementById('notification');

                    if (!selectedPrestationsInfos && info.event.extendedProps.reserved === false) {
                        // Aucune prestation sélectionnée, afficher la notification
                        notification.style.display = 'block';
                    } else {
                        // Une prestation a été sélectionnée, cacher la notification
                        notification.style.display = 'none';
                    }

                    if (selectedPrestationsInfos.trim() !== '') {
                        try {
                            prestation = JSON.parse(selectedPrestationsInfos);
                        } catch (e) {
                            console.error('Erreur lors de l\'analyse de selectedPrestationsInfos:', e);
                        }
                    }


                    prestationList.innerHTML = '';
                    console.log(prestation);
                    const prestations = info.event._def.extendedProps.prestations;
                    if (Array.isArray(prestation)) {
                        prestation.forEach(prestation => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `${prestation.name} - ${prestation.duree} minutes`;
                            prestationList.appendChild(listItem);
                        });
                    } else {
                        // Si prestations n'est pas un tableau, affichez-le directement
                        const listItem = document.createElement('li');
                        listItem.textContent = `${prestations.name} - ${prestations.duration} minutes`;
                        prestationList.appendChild(listItem);
                    }

                    const trashButton = document.querySelector('.icon-trash');

                    // Vérifier si un écouteur d'événements a déjà été ajouté
                    if (!trashButton.hasListener) {
                        trashButton.addEventListener('click', function() {
                            // Récupérer l'ID du rendez-vous à supprimer
                            const appointmentId = info.event.id; // Assurez-vous que vos rendez-vous ont un ID unique

                            // Envoyer une requête AJAX pour supprimer le rendez-vous
                            fetch('/calendar/delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Uncomment this line if you're using Laravel
                                },
                                body: JSON.stringify({
                                    id: appointmentId
                                })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    // Vérifier si la suppression a réussi
                                    if (data.success) {
                                        location.reload();
                                    } else {
                                        // Gérer l'erreur de suppression
                                        console.error('Failed to delete appointment:', data.error);
                                    }
                                })
                                .catch((error) => {
                                    console.error('Error:', error);
                                })
                        .finally(() => {
                                // Recharger les événements du calendrier
                                calendar.refetchEvents();
                            });
                        });

                        calendar.render();

                        // Marquer le bouton de la poubelle comme ayant un écouteur d'événements
                        trashButton.hasListener = true;
                    }

                    const start = info.event.start;
                    const startTime = start.toISOString().slice(0, 19).replace('T', ' ');

                   if(info.event.extendedProps.reserved === true) {
                       const appointmentCard = document.getElementById('appointment-card');

                       var appointmentDetailElements = document.querySelectorAll('.appointment-detail p');

                       var options = { month: 'long', day: 'numeric' };
                       var formattedDate = start.toLocaleDateString('fr-FR', options);

                       // Mettre à jour les éléments de la carte avec les informations de l'événement
                       appointmentDetailElements[0].textContent = 'Le ' + formattedDate + ' de ' + info.event.extendedProps.start_time + ' à ' + info.event.extendedProps.end_time;
                       appointmentDetailElements[1].textContent = 'Employée: ' + info.event.extendedProps.employee.name; // Mettre à jour le nom de l'employé
                       appointmentDetailElements[2].textContent  = 'Client: ' + info.event.extendedProps.client.name;
                       appointmentDetailElements[3].textContent  = 'Email client: ' + info.event.extendedProps.client.email;
                       var prestationsList = document.createElement('ul');
                       info.event.extendedProps.prestations.forEach(function(prestation) {
                           var listItem = document.createElement('li');
                           listItem.textContent = prestation.name + ' (Durée : ' + prestation.duration + ' minutes)';
                           prestationsList.appendChild(listItem);
                       });
                       appointmentDetailElements[4].innerHTML = '';
                       appointmentDetailElements[4].appendChild(prestationsList);

                       // Positionner le container-card à gauche de l'événement

                       const eventRect = info.el.getBoundingClientRect();
                       const eventBottom = eventRect.bottom - 300;
                       const eventLeft = eventRect.left - 450;

// Set the position of the appointmentCard to the bottom left corner of the event
                       appointmentCard.style.top = `${eventBottom}px`;
                       appointmentCard.style.left = `${eventLeft}px`;

// Remove the 'slide-in' class before adding the new animation rule
                       appointmentCard.classList.remove('slide-in');

// Remove the previous animation rule
                       const styleSheet = document.styleSheets[0];
                       const previousRuleIndex = styleSheet.cssRules.length - 1;
                       if (styleSheet.cssRules[previousRuleIndex].name === 'slideInFromRight') {
                           styleSheet.deleteRule(previousRuleIndex);
                       }

// Update the animation to start from the position of the event and end slightly to the left
                       const keyframes = `@keyframes slideInFromRight {
    0% {
        left: ${eventLeft}px;
        opacity: 0;
    }
    100% {
        left: ${eventLeft - 30}px;
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

                document.getElementById('totalDuration').value = totalPrestationDurationMinutes;

                const notification = document.getElementById('notification');
                notification.style.display = 'none';

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
