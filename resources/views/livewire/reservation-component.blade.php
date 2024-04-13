<div>
    <style>

        .div-responsive {
            display: none;
        }

        .laptop {
            display: block;
        }

        /* Affiche le div pour les écrans d'au moins 600px de large */
        @media (max-width: 900px) {
            .div-responsive {
                display: block;
            }
            .laptop {
                display: none;
            }
        }

        .collapse {
            visibility: visible;
        }

        swiper-container::part(button-prev){
            left: 20px;
            top: 50px;
            max-height: 20px;
        }

        swiper-container::part(button-next){
            right: 20px;
            top: 50px;
            max-height: 20px;
        }

        .col {
            min-width: 100px;
        }

        .swiper-slide {
            margin-left: auto; /* Centre le contenu si les marges sont égales des deux côtés */
            margin-right: auto; /* Centre le contenu si les marges sont égales des deux côtés */
            max-width: 95%; /* Ou une autre valeur pour contrôler la largeur */
        }

        .content {
            position: relative;
            background-image: url('/images/background-home.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            height: calc(100vh - 80px); /* Ajuster la hauteur pour laisser de l'espace pour la navbar */
            padding-top: 10px; /* Ajouter un padding pour décaler le contenu */
            overflow-y: auto;
        }

        .overlay {
            position: fixed;
            top: 65px;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            z-index: 1;
        }

        .content-inner {
            position: relative;
            z-index: 2;
            /* Votre contenu ici */
        }

    </style>
    <div class="content">
        <div class="overlay"></div>
        <div class="content-inner">
            <!-- Affichage des prestations -->
            <div class="m-3 mx-auto max-w-screen-lg px-4 lg:px-12" style="font-size: 30px">
                <div class="inline-block">
                    @if(count($selectedPrestations) !== 0)
                        <h1><span style="color: dodgerblue">1.</span> Choix de la prestation</h1>
                    @else
                        <h1><span style="color: dodgerblue">1.</span> Prestation sélectionnée</h1>
                    @endif
                </div>
            </div>

            <section class="mt-2">
                <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
                    <div class="mb-4 d-flex justify-content-center bg-white rounded-lg shadow">
                        <div class="col">
                            @if(count($selectedPrestations) === 0 || $showAddPrestationDiv)
                                <div class="col">
                                    @foreach ($prestations as $prestation)
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model.live="selectedPrestations" value="{{ $prestation->id }}" id="prestation-{{ $prestation->id }}">
                                                <label class="form-check-label" for="prestation-{{ $prestation->id }}">
                                                    {{ $prestation->nom }} ({{ $prestation->temps }} min - {{ $prestation->prix }} €)
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(count($selectedPrestations) !== 0 || $showAddPrestationDiv)

                                <div class="card-body justify-content-between border-none ml-10 mr-20 mb-2">
                                    <div class="mb-2">
                                        <h1>Avec qui?</h1>
                                    </div>
                                    <div class="row d-flex justify-center m-3">
                                        @foreach ($employees as $employee)
                                            <div class="col-auto">
                                                <div class="card">
                                                    <div class="card-body d-flex align-items-center">
                                                        <label class="form-check-label rounded-circle text-center" for="employee-{{ $employee->id }}" style="width: 35px; height: 35px; line-height: 35px; background: #000; color: #fff;">
                                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                        </label>
                                                        <h5 class="card-title ml-2 mr-20 ">{{ $employee->name }}</h5>
                                                        <div class="form-check form-check-inline" style="margin-right: -10px;">
                                                            <input class="form-check-input" type="radio" wire:model.live="selectedEmployee" value="{{ $employee->id }}" id="employee-{{ $employee->id }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if(count($selectedPrestations) !== 0)
                                    <button wire:click="toggleAddPrestationDiv" class="btn btn-secondary">Ajouter une prestation</button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            @if(count($selectedPrestations) !== 0 && $selectedEmployee)
                <div class="laptop">
                    <div class="mx-auto max-w-screen-lg px-4 lg:px-12" style="font-size: 30px">
                        <div class="inline-block">
                            <h1><span style="color: dodgerblue">2.</span> Créneaux disponibles</h1>
                        </div>
                    </div>
                    <section class="mt-2">
                        <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
                            @php
                                $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                                $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                                $currentWeekStart = $startOfMonth->copy();
                            @endphp
                            <swiper-container class="mySwiper" navigation="true">
                                @php
                                    $currentWeekStart = $startOfMonth->copy();
                                    $oneMonthLater = $currentWeekStart->copy()->addMonth();
                                @endphp
                                @while($currentWeekStart->lt($oneMonthLater))
                                    @php
                                        $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
                                    @endphp
                                    <swiper-slide>
                                        <div class="week-container mb-4 d-flex justify-content-center bg-white rounded-lg shadow" style="min-height: 50vh; overflow-x: auto;">
                                            <div class="row flex-nowrap">
                                                @while($currentWeekStart->lte($currentWeekEnd))
                                                    @php
                                                        $formattedDay = $currentWeekStart->format('Y-m-d');
                                                    @endphp
                                                    <div class="col" style="min-width:120px; text-align: center; padding: 3px" wire:key="week-day-{{ $formattedDay }}">
                                                        <div class="mb-3 mt-3 align-items-center justify-content-center">
                                                            <h5>{{ $currentWeekStart->format('l') }}</h5>
                                                            <h5 style="color: gray; font-weight: bold">{{ $currentWeekStart->format('d M') }}</h5>
                                                        </div>
                                                        @foreach($availableSlots as $slot)
                                                            @if($slot['date'] == $formattedDay)
                                                                <div>
                                                                <span class="badge bg-gray-200 mb-2" style="font-weight: normal; color: black; font-size:14px; padding: 13px 40px; border-radius: 10px;">
                                                                    {{ $slot['start'] }}
                                                                </span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @php $currentWeekStart->addDay(); @endphp
                                                @endwhile
                                            </div>
                                        </div>
                                    </swiper-slide>
                                    @php
                                        $currentWeekStart = $currentWeekEnd->copy()->addDay();
                                    @endphp
                                @endwhile
                            </swiper-container>
                        </div>
                    </section>
                    <div class="mx-auto max-w-screen-lg px-4 lg:px-12 mt-4">
                        <button wire:click="confirmReservation" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>


