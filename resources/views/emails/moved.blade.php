@component('mail::message')
    # Rendez-vous déplacé

    Bonjour {{ $client->name }},

    Votre rendez-vous a été déplacé à un autre employé.

    Détails du rendez-vous :
    - Date et heure : {{ \Carbon\Carbon::parse($appointment->start_time)->format('d-m-Y H:i') }}
{{--    - Employé : {{ $appointment->employee->name }}--}}

    Merci,
    {{ config('app.name') }}
@endcomponent
