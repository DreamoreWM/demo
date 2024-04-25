<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['review_id', 'filename'];

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
