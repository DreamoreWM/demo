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
    <li>Date et Heure : {{ $slot->start_time->format('d/m/Y H:i') }} - {{ $slot->end_time->format('H:i') }}</li>
    <li>Client : {{ $user->name }}</li>
    <li>Email du client : {{ $user->email }}</li>
</ul>
<p>Veuillez vérifier votre agenda pour plus de détails.</p>
<p>Merci,</p>
<p>Votre système de réservation</p>
</body>
</html>
