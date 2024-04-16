<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de Réservation</title>
</head>
<body>
<p>Un nouveau créneau a été réservé sur votre agenda :</p>
<ul>
    <li>Date et Heure : {{ $appointment->start_time }} au {{ $appointment->end_time }}</li>
    <li>Client : {{ $user->name }}</li>
    <li>Email du client : {{ $user->email }}</li>
</ul>

<h3>Prestations réservées :</h3>
<ul>
    @foreach ($prestations as $prestation)
        <li>{{ $prestation->nom }} ({{ $prestation->temps }} minutes)</li>
    @endforeach
</ul>
<p>Veuillez vérifier votre agenda pour plus de détails.</p>
<p>Merci,</p>
<p>Votre système de réservation</p>
</body>
</html>
