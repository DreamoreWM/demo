@component('mail::message')
    # Rendez-vous annulé

    Bonjour {{ $client->name }},

    Votre rendez-vous a été annulé.

    Détails du rendez-vous :
    - Date et heure : {{ \Carbon\Carbon::parse($appointment->start_time)->format('d-m-Y H:i') }}
{{--    - Employé : {{ $appointment->employee->name }}--}}

    Nous nous excusons pour le désagrément.

    Merci,
    {{ config('app.name') }}
@endcomponent
