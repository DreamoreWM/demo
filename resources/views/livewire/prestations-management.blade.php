<div>
    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="addPrestation">
        <input type="text" wire:model="nom" placeholder="Nom">
        <textarea wire:model="description" placeholder="Description"></textarea>
        <input type="number" wire:model="prix" placeholder="Prix">
        <input type="number" wire:model="temps" placeholder="Temps (en minutes)">
        <button type="submit">Ajouter</button>
    </form>

    <div class="row">
        @foreach($prestations as $prestation)
            <div class="col-md-4 mb-4">
                <div class="card" wire:key="prestation-{{ $prestation->id }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $prestation->nom }}</h5>
                        <p class="card-text">{{ $prestation->description }}</p>
                        <p>Prix : {{ $prestation->prix }} €</p>
                        <p>Temps : {{ $prestation->temps }} minutes</p>
                        <!-- Bouton de suppression -->
                        <button wire:click="deletePrestation({{ $prestation->id }})" class="btn btn-danger">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
