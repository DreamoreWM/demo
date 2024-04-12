<div>
    <h1>Réservation</h1>

    <div>
        <label for="prestation">Choisissez une ou plusieurs prestations :</label>
        <div class="row">
            @foreach ($prestations as $prestation)
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.live="selectedPrestation" value="{{ $prestation->id }}" id="prestation-{{ $prestation->id }}">
                        <label class="form-check-label" for="prestation-{{ $prestation->id }}">
                            {{ $prestation->nom }} ({{ $prestation->temps }} min - {{ $prestation->prix }} €)
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        <label for="employee">Choisissez un employé :</label>
        <div class="row">
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


    @if($selectedPrestation && $selectedEmployee)
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
                            <!-- Affichage hebdomadaire -->
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
                                // Préparer le début de la semaine suivante
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
