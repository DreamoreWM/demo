<link rel="stylesheet" href="fontawesome/css/all.min.css">
<link rel="stylesheet" href="css/templatemo-style.css">

<style>

    .loader {
        top: 50vh !important;
        bottom: 0;
        right: 0;
        align-items: center;
        background: rgba(0, 0, 0, 0.5);
    }

    .loader-content {
        visibility: hidden;
    }

    .home {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%; /* Change this */
    }

    section {
        width: 100vw;
    }

    .portfolio {
        justify-content: center;
        align-items: center;
        background-image: url('/images/second-font.jpg');
        background-size: cover; /* Couvre toute la zone disponible sans redimensionner l'image */
        background-position: center;
        background-repeat: no-repeat; /* Empêche la répétition de l'image */
        background-attachment: fixed;
    }

    .prestation {
        background-size: cover; /* Couvre toute la zone disponible sans redimensionner l'image */
        background-repeat: no-repeat; /* Empêche la répétition de l'image */
        background-attachment: fixed;
        justify-content: center;
        align-items: center;
    }

    .info {
        justify-content: center;
        align-items: center;
        background-color: #a1a1a1;
    }

    .photo {
        margin-bottom: -15px;
    }

    .gsi-material-button {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        -webkit-appearance: none;
        background-color: WHITE;
        background-image: none;
        border: 1px solid #747775;
        -webkit-border-radius: 20px;
        border-radius: 20px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        color: #1f1f1f;
        cursor: pointer;
        font-family: 'Roboto', arial, sans-serif;
        font-size: 14px;
        height: 40px;
        letter-spacing: 0.25px;
        outline: none;
        overflow: hidden;
        padding: 0 12px;
        position: relative;
        text-align: center;
        -webkit-transition: background-color .218s, border-color .218s, box-shadow .218s;
        transition: background-color .218s, border-color .218s, box-shadow .218s;
        vertical-align: middle;
        white-space: nowrap;
        width: auto;
        max-width: 400px;
        min-width: min-content;
    }

    .gsi-material-button .gsi-material-button-icon {
        height: 20px;
        margin-right: 12px;
        min-width: 20px;
        width: 20px;
    }

    .gsi-material-button .gsi-material-button-content-wrapper {
        -webkit-align-items: center;
        align-items: center;
        display: flex;
        -webkit-flex-direction: row;
        flex-direction: row;
        -webkit-flex-wrap: nowrap;
        flex-wrap: nowrap;
        height: 100%;
        justify-content: space-between;
        position: relative;
        width: 100%;
    }

    .gsi-material-button .gsi-material-button-contents {
        -webkit-flex-grow: 1;
        flex-grow: 1;
        font-family: 'Roboto', arial, sans-serif;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: top;
    }

    .gsi-material-button .gsi-material-button-state {
        -webkit-transition: opacity .218s;
        transition: opacity .218s;
        bottom: 0;
        left: 0;
        opacity: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    .gsi-material-button:disabled {
        cursor: default;
        background-color: #ffffff61;
        border-color: #1f1f1f1f;
    }

    .gsi-material-button:disabled .gsi-material-button-contents {
        opacity: 38%;
    }

    .gsi-material-button:disabled .gsi-material-button-icon {
        opacity: 38%;
    }

    .gsi-material-button:not(:disabled):active .gsi-material-button-state,
    .gsi-material-button:not(:disabled):focus .gsi-material-button-state {
        background-color: #303030;
        opacity: 12%;
    }

    .gsi-material-button:not(:disabled):hover {
        -webkit-box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
    }

    .gsi-material-button:not(:disabled):hover .gsi-material-button-state {
        background-color: #303030;
        opacity: 8%;
    }

    footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }
    .content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-image: url('/images/background-home.webp');
        background-size: cover; /* Couvre toute la zone disponible sans redimensionner l'image */
        background-position: center;
        background-repeat: no-repeat; /* Empêche la répétition de l'image */
        background-attachment: fixed; /* Fixe l'arrière-plan par rapport à la fenêtre du navigateur */
        /* Définissez une hauteur fixe ou utilisez flexbox/grid pour définir la hauteur */
        margin: 0; /* Reset margin */
        padding: 0;
        height: 95vh;
    }

    @media (max-height: 580px) {
        .content {
            height: auto;
        }
    }
    .booking-btn:hover {
        background-color: #45a049; /* Un peu plus foncé que le couleur du bouton */
        box-shadow: 0 2px #666; /* Ombre plus proche pour effet au survol */
        transform: translateY(-2px); /* Déplace légèrement le bouton vers le haut pour l'effet de clic */
    }

    .booking-btn:active {
        background-color: #3e8e41; /* Encore plus foncé pour l'effet de clic */
        box-shadow: 0 1px #666; /* Ombre très proche pour l'effet de clic */
        transform: translateY(1px); /* Déplace légèrement le bouton vers le bas pour l'effet de clic */
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<x-app-layout>

    <div id="loader" class="loader"></div>

    <div id="loader-content" class="loader-content">
        <div id="content" class="content">
            <div class="py-6">
                @if(session()->has('success'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x-circle"></i></button>
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x-circle"></i></button>
                    </div>
                @endif
                <div class="home" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="background: rgba(255, 255, 255, 0.8); margin-top: 4vmin; padding: 15px; border-radius: 50%; display: inline-flex; justify-content: center; align-items: center;">
                        <img src="/images/logo.png" alt="Logo Salon de Coiffure" style="max-width: 160px;"> <!-- Ajustez max-width en fonction de la taille réelle de votre logo -->
                    </div>
                    {{--                @if($isOpen)--}}
                    {{--                    <img src="/images/open.png" alt="Logo ouvert" style="max-width: 160px;"> <!-- Ajustez max-width en fonction de la taille réelle de votre logo -->--}}

                    {{--                @else--}}
                    {{--                    <img src="/images/close.png" alt="Logo fermé" style="max-width: 160px;"> <!-- Ajustez max-width en fonction de la taille réelle de votre logo -->--}}

                    {{--                @endif--}}
                    <div>

                    </div>
                    @if(Auth::check())
                        <a href="{{ route('appointments.create') }}" class="booking-btn" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; font-weight: bold; border-radius: 5px; box-shadow: 0 4px #999; transition: all 0.3s; margin-top: 20px;">
                            Prendre Rendez-Vous
                        </a>
                    @else
                        <!-- Bouton qui ouvre la modal pour les utilisateurs non connectés -->
                        <button class="booking-btn" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; font-weight: bold; border-radius: 5px; box-shadow: 0 4px #999; transition: all 0.3s; margin-top: 20px;" data-toggle="modal" data-target="#loginModal">
                            Prendre un rendez-vous
                        </button>
                    @endif

                    <div style="
    font-size: 10px;
    font-weight: bold;
    color: white;
    min-width: 300px;
    padding: 10px;
    margin: 2vmin;
    border-radius: 15px;
    backdrop-filter: blur(60px);
    box-shadow: 0px 4px 14px 0px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.1);
">

                        <div style="display: flex; align-items: center; padding-bottom: 10px">
                            <div style="flex-grow: 1; border-bottom: 3px solid white;"></div>
                            <div style="padding: 0 7px; font-weight: bold">Nos horaires</div>
                            <div style="flex-grow: 1; border-bottom: 3px solid white;"></div>
                        </div>

                        <!-- Insert the dynamic opening hours here -->
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <ul>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>{{ strtoupper($day) }}</span>
                                    <span>
                @if(isset($openDays[$day]))
                                            {{ $openDays[$day]['open'] }} / {{ $openDays[$day]['break_start'] }} - {{ $openDays[$day]['break_end'] }} / {{ $openDays[$day]['close'] }}
                                        @else
                                            Fermé
                                        @endif
            </span>
                                </div>
                            </ul>
                        @endforeach
                    </div>

                    <div>
                        @include('reviews.index', ['reviews' => $reviews])
                    </div>


                </div>

            </div>
        </div>

        <div class="prestation" data-aos="fade-down">
            <section>
                <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="col pb-10">
                            <h2 class=" pt-10 text-center font-bold" data-aos="fade-down" data-aos-offset="250">LES TARIFS</h2>
                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-md-6" data-aos="fade-up" data-aos-offset="450">
                                        <div class="card mt-4 border-0 bg-transparent">
                                            <div class="card-header bg-transparent">
                                                <h3 class="text-left font-bold">{{ strtoupper($category->name) }}</h3>
                                            </div>
                                            <div class="card-body">
                                                @foreach($category->prestations as $prestation)
                                                    <p class="d-flex justify-content-between">
                                                        <span>{{ $prestation->nom }}</span>
                                                        <span>{{ $prestation->prix }} EUR</span>
                                                    </p>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="portfolio" data-aos="fade-right" data-aos-offset="250">
            <div class="container-fluid tm-container-content" >
                <div class="row tm-gallery pt-5" style="justify-content: center !important">
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5"  data-aos="fade-up" data-aos-offset="450">
                        <figure class="effect-ming tm-video-item">
                            <img src="img/img-14.jpg" alt="Image" class="img-fluid">
                            <figcaption class="d-flex align-items-center justify-content-center">
                                <h2>Sea</h2>
                                <a href="photo-detail.html">View more</a>
                            </figcaption>
                        </figure>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5"  data-aos="fade-up" data-aos-offset="450">
                        <figure class="effect-ming tm-video-item">
                            <img src="img/img-15.jpg" alt="Image" class="img-fluid">
                            <figcaption class="d-flex align-items-center justify-content-center">
                                <h2>Turtle</h2>
                                <a href="photo-detail.html">View more</a>
                            </figcaption>
                        </figure>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5"  data-aos="fade-up"  data-aos-offset="450">
                        <figure class="effect-ming tm-video-item">
                            <img src="img/img-16.jpg" alt="Image" class="img-fluid">
                            <figcaption class="d-flex align-items-center justify-content-center">
                                <h2>Peace</h2>
                                <a href="photo-detail.html">View more</a>
                            </figcaption>
                        </figure>
                    </div>
                </div> <!-- row -->
            </div>
        </div>

        <div class="info" data-aos="fade-down" data-aos-offset="250">
            <section>
                <div class="mx-auto max-w-screen-lg px-4 lg:px-12">
                    <div class="mb-4">
                        <div class="col">
                            <div class="row">
                                <div class="col-md-6" data-aos="fade-right" >
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-body">
                                            <iframe src="https://maps.google.com/maps?q=4+rue+du+téléphone+59%2670+croix+france&output=embed" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" data-aos="fade-right" >
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-body">
                                            <iframe name="fca9f924a1dc1fe6f" height="450px" data-testid="fb:page Facebook Social Plugin" title="fb:page Facebook Social Plugin" frameborder="0" allowtransparency="true" allowfullscreen="true" scrolling="no" allow="encrypted-media" src="https://www.facebook.com/v2.10/plugins/page.php?app_id=&amp;container_width=450&amp;height=450&amp;hide_cover=false&amp;hide_cta=false&amp;href=https%3A%2F%2Fwww.facebook.com%2F{{$facebookPageUrl}}&amp;locale=fr_FR&amp;sdk=joey&amp;show_facepile=true&amp;small_header=false&amp;tabs=timeline&amp;width=500px" style="border: none; visibility: visible; width: 450px; height: 450px;" class=""></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Structure de la modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Connexion</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Formulaire de Connexion -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">Adresse Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>
                            <div class="buttons-container d-flex justify-content-between">
                                <button class="btn btn-primary">Se connecter</button>
                                <button type="button" onclick="location.href='{{ route('auth.google') }}'" class="gsi-material-button">
                                    <div class="gsi-material-button-state"></div>
                                    <div class="gsi-material-button-content-wrapper">
                                        <div class="gsi-material-button-icon">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                                <path fill="none" d="M0 0h48v48H0z"></path>
                                            </svg>
                                        </div>
                                        <span class="gsi-material-button-contents">Sign in with Google</span>
                                        <span style="display: none;">Sign in with Google</span>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>



    <script>
        window.onload = function() {
            document.getElementById('loader').style.display = "none";
            document.getElementById('loader-content').style.visibility = "visible";
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>



    {{--    <footer style="background-color: #767473; padding: 10px; text-align: center; border-top: 1px solid #e7e7e7;">--}}
{{--        <div style="margin-bottom: 10px;">--}}
{{--            <a href="/about" style="margin-right: 15px; text-decoration: none; color: #FFFFFF;">À propos</a>--}}
{{--            <a href="/services" style="margin-right: 15px; text-decoration: none; color: #FFFFFF;">Services</a>--}}
{{--            <a href="/contact" style="text-decoration: none; color: #FFFFFF;">Contact</a>--}}
{{--        </div>--}}
{{--        <div>--}}
{{--            <p style="margin: 0; color: #E2E2E2;">© 2024 MonSiteWeb. Tous droits réservés.</p>--}}
{{--            <p style="margin: 0; color: #E2E2E2;">contact@monsiteweb.com | +33 1 23 45 67 89</p>--}}
{{--        </div>--}}
{{--    </footer>--}}
</x-app-layout>

