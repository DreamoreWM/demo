<div>
    <style>
        .collapse.show {
            visibility: visible;
        }
    </style>
    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <section class="mt-2">
        <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
            <div class="mb-4 d-flex justify-content-center bg-white rounded-lg shadow">
                <div class="col">
                    <div class="accordion" id="categoryAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-category">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-category" aria-expanded="false" aria-controls="collapse-category">
                                    AJOUTER UNE CATEGORIE
                                </button>
                            </h2>

                            <div id="collapse-category" class="accordion-collapse collapse" aria-labelledby="heading-category" data-bs-parent="#categoryAddAccordion">
                                <div class="accordion-body">
                                    <div class="col">
                                        @livewire('add-category')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-2">
        <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
            <div class="mb-4 d-flex justify-content-center bg-white rounded-lg shadow">
                <div class="col">
                        <div class="accordion" id="categoryAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-prestation">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-prestation" aria-expanded="false" aria-controls="collapse-prestation">
                                            AJOUTER UNE PRESTATION
                                        </button>
                                    </h2>

                                    <div id="collapse-prestation" class="accordion-collapse collapse" aria-labelledby="heading-prestation" data-bs-parent="#categoryAccordion">
                                        <div class="accordion-body">
                                            <div class="col">
                                                <form wire:submit.prevent.live="addPrestation" class="p-3">
                                                    <div class="form-group">
                                                        <label for="nom">Nom</label>
                                                        <input type="text" class="form-control" id="nom" wire:model="nom" placeholder="Nom">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control" id="description" wire:model="description" placeholder="Description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="prix">Prix</label>
                                                        <input type="number" class="form-control" id="prix" wire:model="prix" placeholder="Prix">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="temps">Temps (en minutes)</label>
                                                        <input type="number" class="form-control" id="temps" wire:model="temps" placeholder="Temps (en minutes)">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="category_id">Category</label>
                                                        <select class="form-control" id="category_id" wire:model="category_id">
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-2">
        <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
            <div class="mb-4 d-flex justify-content-center bg-white rounded-lg shadow">
                <div class="col">
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="col">
                            <div class="accordion" id="categoryAccordion">
                                @foreach($categories as $category)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $category->id }}" style="display: flex">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $category->id }}" aria-expanded="false" aria-controls="collapse{{ $category->id }}">
                                                {{ $category->name }}<!-- Bouton de suppression de la catégorie -->
                                            </button>
                                            <button wire:click="deleteCategory({{ $category->id }})" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </h2>

                                        <div id="collapse{{ $category->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $category->id }}" data-bs-parent="#categoryAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach($category->prestations as $prestation)
                                                        <div class="col-md-4 mb-4">
                                                            <div class="card" wire:key="prestation-{{ $prestation->id }}">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">{{ $prestation->nom }}</h5>
                                                                    <p class="card-text">{{ $prestation->description }}</p>
                                                                    <p>Prix : {{ $prestation->prix }} €</p>
                                                                    <p>Temps : {{ $prestation->temps }} minutes</p>
                                                                    <button wire:click="deletePrestation({{ $prestation->id }})" class="btn btn-danger">
                                                                        Supprimer
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>






</div>
