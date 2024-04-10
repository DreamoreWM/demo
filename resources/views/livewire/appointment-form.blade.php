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
                    @if(!$selectedPrestation)
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
                            @if(!$selectedPrestation || $showAddPrestationDiv)
                                <div class="col">
                                    @foreach($prestations as $prestation)
                                        <div class="card m-3">
                                            <!-- Utilisez d-flex et justify-content-between pour séparer les éléments -->
                                            <div class="card-body d-flex justify-content-between">
                                                <!-- Titre aligné à gauche -->
                                                <h5 class="card-title">{{ $prestation->nom }}</h5>
                                                <!-- Conteneur pour le texte et le bouton alignés à droite -->
                                                <div class="d-flex" style="color: gray">
                                                    <p>{{ $prestation->temps }} min</p>
                                                    <p class="ml-2 mr-2"> • </p>
                                                    <p style="font-weight: bold;">{{ $prestation->prix }} €</p>
                                                    <button wire:click="selectPrestation({{ $prestation->id }})" class="btn btn-primary ml-5">Sélectionner</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>





                            @endif
                            @if($selectedPrestation || $showAddPrestationDiv)
                                @foreach($selectedPrestations as $index => $prestation)
                                    <div>
                                        <div class="card-body d-flex justify-content-between border-none ml-10 mr-20  mt-8 mb-2">
                                            <!-- Titre aligné à gauche -->
                                            <div class="font-bold">
                                                <p>{{ $prestation->nom }}</p>
                                                <p class="text-gray-400">{{ $prestation->temps }} min<span class="ml-2 mr-2"> • </span> {{ $prestation->prix }} €</p>
                                            </div>
                                            <!-- Conteneur pour le texte et le bouton alignés à droite -->
                                            <div wire:key="selected_prestation_{{ $index }} class="d-flex">
                                            <div>
                                                <button wire:click="deletePrestation({{ $index }})" class="text-red-500">Supprimer</button>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                        @endforeach


                        <div class="card-body justify-content-between border-none ml-10 mr-20 mb-2">
                            <div class="mb-2">
                                <h1>Avec qui?</h1>
                            </div>
                            <div class="row d-flex justify-center m-3">
                                @foreach($employees as $employee)
                                    <div class="col-auto">
                                        <div class="card">
                                            <div class="card-body d-flex align-items-center">
                                                <label class="form-check-label rounded-circle text-center" for="employee{{ $employee->id }}" style="width: 35px; height: 35px; line-height: 35px; background: #000; color: #fff;">
                                                    <!-- Affichez la première lettre du nom de l'employé -->
                                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                </label>
                                                <h5 class="card-title ml-2 mr-20 ">{{ $employee->name }}</h5>
                                                <!-- Utilisez custom-radio pour personnaliser l'apparence -->
                                                <div class="form-check form-check-inline" style="margin-right: -10px;">
                                                    <input class="form-check-input" type="radio" name="selectedEmployeeId" wire:model.lazy="selectedEmployeeId" value="{{ $employee->id }}" id="employee{{ $employee->id }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($selectedPrestation)
                            <button wire:click="toggleAddPrestationDiv" class="btn btn-secondary">Ajouter une prestation</button>
                        @endif

                    </div>

                </div>

        </div>
        </section>




        @if($selectedPrestation)
            <div class="laptop">
                <div class="mx-auto max-w-screen-lg px-4 lg:px-12" style="font-size: 30px">
                    <div class="inline-block">
                        <h1><span style="color: dodgerblue">2.</span> Choix de la date</h1>
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
                                $currentWeekStart = $this->currentWeekStartDate->copy();
                                $oneMonthLater = $currentWeekStart->copy()->addMonth();
                            @endphp
                                <!-- Affichage hebdomadaire -->
                            @while($currentWeekStart->lt($oneMonthLater))
                                @php
                                    $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
                                @endphp
                                <swiper-slide>

                                    <div class=" week-container mb-4 d-flex justify-content-center bg-white rounded-lg shadow" style="min-height: 50vh; overflow-x: auto;">
                                        <div class="row flex-nowrap"    >
                                            @while($currentWeekStart->lte($currentWeekEnd))
                                                @php
                                                    $formattedDay = $currentWeekStart->format('Y-m-d');
                                                @endphp
                                                <div class="col" style="min-width:120px; text-align: center; padding: 3px" wire:key="week-day-{{ $formattedDay }}">
                                                    <div class="mb-3 mt-3 align-items-center justify-content-center">
                                                        <h5>{{ $currentWeekStart->format('l') }}</h5>
                                                        <h5 style="color: gray; font-weight: bold">{{ $currentWeekStart->format('d M') }}</h5>
                                                    </div>
                                                    @php
                                                        $firstOfGroupDisplayed = false;
                                                    @endphp
                                                    @foreach($slots as $employeeSlots)
                                                        @foreach($employeeSlots as $sequence)
                                                            @if(!$firstOfGroupDisplayed && count($sequence) > 0 && \Carbon\Carbon::parse($sequence[0]['date'])->format('Y-m-d') === $formattedDay)
                                                                @php
                                                                    $firstOfGroupDisplayed = true;
                                                                @endphp
                                                                <div>
                        <span wire:click="confirmItemDeletion({{ $sequence[0]['id'] }})" class="badge bg-gray-200 mb-2" style="font-weight: normal; color: black; font-size:14px; padding: 13px 40px; border-radius: 10px;">
                            {{ \Carbon\Carbon::parse($sequence[0]['start_time'])->format('H:i') }}
                        </span>
                                                                </div>
                                                            @endif
                                                            @php
                                                                $firstOfGroupDisplayed = false; // Reset pour vérifier le prochain groupe
                                                            @endphp
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                                @php $currentWeekStart->addDay(); @endphp
                                            @endwhile


                                        </div>
                                    </div>

                                </swiper-slide>
                                @php
                                    // Préparer le début de la semaine suivante
                                    $currentWeekStart = $currentWeekEnd->copy()->addDay();
                                @endphp
                            @endwhile

                        </swiper-container>
                </section>
            </div>
        @endif


        @if($selectedPrestation)
            <div class="div-responsive">
                <div class="mx-auto max-w-screen-lg px-4 lg:px-12" style="font-size: 30px">
                    <div class="inline-block">
                        <h1><span style="color: dodgerblue">2.</span> Choix de la date</h1>
                    </div>
                </div>
                <section class="mt-2">
                    <div class="mx-auto max-w-screen-lg px-4 lg:px-12">

                        @php
                            $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                            $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                            $currentWeekStart = $startOfMonth->copy();
                        @endphp


                        @php
                            $currentWeekStart = $this->currentWeekStartDate->copy();
                            $oneMonthLater = $currentWeekStart->copy()->addMonth();
                        @endphp
                            <!-- Affichage hebdomadaire -->
                        @while($currentWeekStart->lt($oneMonthLater))
                            @php
                                $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
                            @endphp


                            <div class="week-container mb-4 d-flex align-items-center justify-center justify-content-center bg-white rounded-lg shadow" style="min-height: 50vh; overflow-x: auto;">
                                <div class="col flex-nowrap">


                                    @php $weekDayIndex = 0; @endphp
                                    @while($currentWeekStart->lte($currentWeekEnd))
                                        @php
                                            $formattedDay = $currentWeekStart->format('Y-m-d');
                                            // Générer des identifiants uniques pour cette itération
                                            $accordionId = 'dateAccordion' . $weekDayIndex;
                                            $headingId = 'flush-heading' . $weekDayIndex;
                                            $collapseId = 'flush-collapse' . $weekDayIndex;
                                        @endphp
                                        @php
                                            $hasValidSlots = false;
                                            foreach($slots as $employeeId => $employeeSlots) {
                                                foreach($employeeSlots as $index => $sequence) {
                                                    if(count($sequence) > 0) {
                                                        $firstSlotOfGroup = $sequence[0];
                                                        $date = \Carbon\Carbon::parse($firstSlotOfGroup['date']);
                                                        $dateR = $date->format('Y-m-d');
                                                        if($dateR == $formattedDay) {
                                                            $hasValidSlots = true;
                                                            break 2; // Quitte les deux boucles si un créneau valide est trouvé
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp

                                        <div class="accordion accordion-flush" id="{{ $accordionId }}">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="{{ $headingId }}">
                                                    <button class="accordion-button {{ $hasValidSlots ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $hasValidSlots ? 'true' : 'false' }}" style="background-color: transparent !important">
                                                        <div class="align-items-center justify-content-center">
                                                            <h5>{{ $currentWeekStart->format('l') }}</h5>
                                                            <h5 style="color: gray; font-weight: bold">{{ $currentWeekStart->format('d M') }}</h5>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $hasValidSlots ? 'show' : '' }}" aria-labelledby="{{ $headingId }}" data-bs-parent="#{{ $accordionId }}">
                                                    <div class="flex-wrap d-flex accordion-body">
                                                        @foreach($slots as $employeeId => $employeeSlots)
                                                            @if(isset($employeeSlots))
                                                                @foreach($employeeSlots as $index => $sequence)
                                                                    @if(count($sequence) > 0)
                                                                        @php
                                                                            $firstSlotOfGroup = $sequence[0]; // Prenez le premier créneau du groupe
                                                                            $date = \Carbon\Carbon::parse($firstSlotOfGroup['date']);
                                                                            $dateR = $date->format('Y-m-d');
                                                                        @endphp
                                                                        @if($dateR == $formattedDay)
                                                                            <div>
                                        <span wire:click="confirmItemDeletion({{ $firstSlotOfGroup['id'] }})" class="badge bg-gray-200 mb-2" style="font-weight: normal; color: black; font-size:14px; padding: 13px 40px; border-radius: 10px;">
                                            {{ \Carbon\Carbon::parse($firstSlotOfGroup['start_time'])->format('H:i') }}
                                        </span>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $currentWeekStart->addDay();
                                            $weekDayIndex++; // Incrémenter l'index pour la prochaine itération
                                        @endphp
                                    @endwhile
                                </div>
                            </div>
                    @php
                        // Préparer le début de la semaine suivante
                        $currentWeekStart = $currentWeekEnd->copy()->addDay();
                    @endphp
                    @endwhile

                </section>
            </div>
        @endif



        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

        <div class="modal fade {{ $confirmingItemDeletion ? 'show' : '' }}" style="display: {{ $confirmingItemDeletion ? 'block' : 'none' }};" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmation Requise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir effectuer cette action ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('confirmingItemDeletion', false)">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="bookSlot">Confirmer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>



</div>
