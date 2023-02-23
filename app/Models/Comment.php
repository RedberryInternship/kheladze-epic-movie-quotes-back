<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'comment',
        'writer_id'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }
}
