<div>
    <form wire:submit.prevent="addCategory">
        <input type="text" wire:model="name" placeholder="Nom de la catégorie">
        <button type="submit">Ajouter</button>
    </form>
</div>
