<p>Bonjour {{ $user->name }},</p>

<p>Votre réservation pour le créneau {{ $appointment->start_time }} au {{ $appointment->end_time }} a été confirmée.</p>

<h3>Prestations réservées :</h3>
<ul>
    @foreach ($prestations as $prestation)
        <li>{{ $prestation->nom }} ({{ $prestation->temps }} minutes)</li>
    @endforeach
</ul>

<p>Merci d'utiliser notre service.</p>
