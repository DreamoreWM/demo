<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0px 0px 30px rgba(0, 0, 0, 1), 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 20px;
        max-width: 500px;
        width: 100%;
        z-index: 1;
    }

    .card-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .card-body {
        text-align: center;
    }

    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button[type="submit"] {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
    }

    .background-blur {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('/images/background-home.webp');
        background-size: cover;
        background-position: center;
        filter: blur(5px);
        z-index: -1;
    }

    #full-stars-example {

        /* use display:inline-flex to prevent whitespace issues. alternatively, you can put all the children of .rating-group on a single line */
        .rating-group {
            display: inline-flex;
        }

        /* make hover effect work properly in IE */
        .rating__icon {
            pointer-events: none;
        }

        /* hide radio inputs */
        .rating__input {
            position: absolute !important;
            left: -9999px !important;
        }

        /* set icon padding and size */
        .rating__label {
            cursor: pointer;
            padding: 0 0.1em;
            font-size: 2rem;
        }

        /* set default star color */
        .rating__icon--star {
            color: orange;
        }

        /* set color of none icon when unchecked */
        .rating__icon--none {
            color: #eee;
        }

        /* if none icon is checked, make it red */
        .rating__input--none:checked + .rating__label .rating__icon--none {
            color: red;
        }

        /* if any input is checked, make its following siblings grey */
        .rating__input:checked ~ .rating__label .rating__icon--star {
            color: #ddd;
        }

        /* make all stars orange on rating group hover */
        .rating-group:hover .rating__label .rating__icon--star {
            color: orange;
        }

        /* make hovered input's following siblings grey on hover */
        .rating__input:hover ~ .rating__label .rating__icon--star {
            color: #ddd;
        }

        /* make none icon grey on rating group hover */
        .rating-group:hover .rating__input--none:not(:hover) + .rating__label .rating__icon--none {
            color: #eee;
        }

        /* make none icon red on hover */
        .rating__input--none:hover + .rating__label .rating__icon--none {
            color: red;
        }
    }
</style>

<form method="POST" action="/reviews" enctype="multipart/form-data">>
    @csrf
    <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
    <div class="background-container">
        <div class="card">
            <div class="card-header">
                <div id="full-stars-example">
                    <div class="rating-group">
                        <input class="rating__input rating__input--none" name="rating" id="rating-none" value="0" type="radio">
                        <label aria-label="No rating" class="rating__label" for="rating-none"><i class="rating__icon rating__icon--none fa fa-ban"></i></label>
                        <label aria-label="1 star" class="rating__label" for="rating-1"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                        <input class="rating__input" name="rating" id="rating-1" value="1" type="radio">
                        <label aria-label="2 stars" class="rating__label" for="rating-2"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                        <input class="rating__input" name="rating" id="rating-2" value="2" type="radio">
                        <label aria-label="3 stars" class="rating__label" for="rating-3"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                        <input class="rating__input" name="rating" id="rating-3" value="3" type="radio" checked>
                        <label aria-label="4 stars" class="rating__label" for="rating-4"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                        <input class="rating__input" name="rating" id="rating-4" value="4" type="radio">
                        <label aria-label="5 stars" class="rating__label" for="rating-5"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                        <input class="rating__input" name="rating" id="rating-5" value="5" type="radio">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <label for="comment">Commentaire :</label>
                <textarea id="comment" name="comment"></textarea>
                <label for="photos">Photos :</label>
                <input type="file" id="photos" name="photos[]" multiple>
                <button type="submit">Envoyer</button>
            </div>
        </div>
    </div>
</form>

<div class="background-blur"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

