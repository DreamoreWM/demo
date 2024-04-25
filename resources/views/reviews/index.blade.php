<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
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
            padding-left: 0;
            font-size: 0.8rem;
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

    .date-rating {
        display: flex;
        justify-content: space-between;
    }

    .date-create {
        font-size: 0.6rem;
    }
    @media (max-width: 580px) {
        #customers-testimonials .item {
            opacity: 1;
        }

        .shadow-effect {
            width: 100%;
        }

        .testimonials{
            max-width: 400px;
        }
    }

        body {
            overflow-x: hidden;
        }


    .image-slider {
        display: flex;
        justify-content: right;
        gap: 10px; /* This will add 10px of space between each image */
        flex-wrap: wrap;
        margin: 0; /* Reset margin */
        padding: 0;
    }

    .review-image {
        width: 30px !important;
        height: 30px !important;
        border-radius: 4px !important; /* This will make the image round */
        object-fit: cover !important; /* This will cover the whole area without distortion */
        cursor: pointer;
        margin: 7px 0px;
    }

    .owl-item img{
        width: 50px;
        height: 50px;
    }

    #image-modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }

    #image-modal-content {
        margin: 15% auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    .owl-nav {
        display: none !important;
    }
    .shadow-effect {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        text-align: left;
        border:1px solid #ECECEC;
        box-shadow: 0 19px 38px rgba(0,0,0,0.10), 0 15px 12px rgba(0,0,0,0.02);
    }
    #customers-testimonials .shadow-effect p {
        font-family: inherit;
        font-size: 12px;
        line-height: 1.5;
        margin: 0 0 5px 0;
        font-weight: 300;
    }
    .testimonial-name {
        padding-bottom: 7px;
    }

    #customers-testimonials .item {
        text-align: center;
        padding: 50px;
        opacity: .2;
        -webkit-transform: scale3d(0.8, 0.8, 1);
        transform: scale3d(0.8, 0.8, 1);
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }
    #customers-testimonials .owl-item.active.center .item {
        opacity: 1;
        -webkit-transform: scale3d(1.0, 1.0, 1);
        transform: scale3d(1.0, 1.0, 1);
    }
    .owl-carousel .owl-item img {
        transform-style: preserve-3d;
        max-width: 90px;
        margin: 0 auto 17px;
    }
    #customers-testimonials.owl-carousel .owl-dots .owl-dot.active span,
    #customers-testimonials.owl-carousel .owl-dots .owl-dot:hover span {
        background: #3190E7;
        transform: translate3d(0px, -50%, 0px) scale(0.7);
    }
    #customers-testimonials.owl-carousel .owl-dots{
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    #customers-testimonials.owl-carousel .owl-dots .owl-dot{
        display: inline-block;
    }
    #customers-testimonials.owl-carousel .owl-dots .owl-dot span {
        background: #3190E7;
        display: inline-block;
        height: 20px;
        margin: 0 2px 5px;
        transform: translate3d(0px, -50%, 0px) scale(0.3);
        transform-origin: 50% 50% 0;
        transition: all 250ms ease-out 0s;
        width: 20px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

{{--@foreach ($reviews as $review)--}}
{{--    <div>--}}
{{--        <p>Note : {{ $review->rating }}</p>--}}
{{--        <p>Commentaire : {{ $review->comment }}</p>--}}
{{--    </div>--}}
{{--@endforeach--}}

<!-- TESTIMONIALS -->
<section class="testimonials">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div id="customers-testimonials" class="owl-carousel">
                    @foreach ($reviews as $review)
                        <!--TESTIMONIAL 1 -->
                        <div class="item">
                            <div class="shadow-effect">
                                <h1 class="testimonial-name">{{ $review->appointment->bookable->name }}</h1>
                                <div class="date-rating">
                                        <div id="full-stars-example">
                                            <div class="rating-group">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        <label aria-label="{{ $i }} star" class="rating__label"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                    @else
                                                        <label aria-label="{{ $i }} star" class="rating__label"><i class="rating__icon rating__icon--star fa fa-star-o"></i></label>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="date-create">
                                            {{ $review->created_at->format('d M Y') }}
                                        </div>
                                </div>
                                <p style="margin-top: 5px">{{ $review->comment }}</p>
                                <div class="image-slider" style="padding-bottom: 5px">
                                    @if(optional($review->photo))
                                        @foreach($review->photo as $photo)
                                            <div class="photo">
                                                <img src="{{ Storage::url('reviews/' . $photo->filename) }}" alt="Photo de la revue" class="review-image" onclick="showImage(this)">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--END OF TESTIMONIAL 1 -->
                    @endforeach
                </div>
            </div>
    </div>
</section>

<div id="image-modal" onclick="this.style.display='none'">
    <span class="close">&times;</span>
    <img id="image-modal-content">
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    function showImage(img) {
        var modal = document.getElementById('image-modal');
        var modalImg = document.getElementById('image-modal-content');
        modal.style.display = "block";
        modalImg.src = img.src;
    }

    jQuery(document).ready(function($) {
        "use strict";
        //  TESTIMONIALS CAROUSEL HOOK
        $('#customers-testimonials').owlCarousel({
            loop: true,
            center: true,
            items: 1,
            padding: 20,
            autoplay: true,
            dots:false,
            autoplayTimeout: 250000000,
            smartSpeed: 450,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1170: {
                    items: 3
                }
            }
        });
    });
</script>
<!-- END OF TESTIMONIALS -->
