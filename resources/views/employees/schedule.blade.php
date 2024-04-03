<form action="{{ route('employees.schedule.store', $employee->id) }}" method="POST">
    @csrf

    <select name="day_of_week">
        <option value="1">Lundi</option>
        <option value="2">Mardi</option>
        <option value="3">Mercredi</option>
        <option value="4">Jeudi</option>
        <option value="5">Vendredi</option>
        <option value="6">Samedi</option>
        <option value="7">Dimanche</option>
    </select>

    <input type="time" name="start_time" placeholder="Heure de Début">
    <input type="time" name="end_time" placeholder="Heure de Fin">

    <button type="submit">Ajouter l'Horaire</button>
</form>
