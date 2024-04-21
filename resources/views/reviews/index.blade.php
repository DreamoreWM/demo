@foreach ($reviews as $review)
    <div>
        <p>Note : {{ $review->rating }}</p>
        <p>Commentaire : {{ $review->comment }}</p>
    </div>
@endforeachphp artisan check:appointments
