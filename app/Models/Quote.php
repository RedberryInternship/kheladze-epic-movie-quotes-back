<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = [
        'quote'
    ];
    protected $fillable = [
        'movie_id',
        'quote',
        'image'
    ];

    public function movies()
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
