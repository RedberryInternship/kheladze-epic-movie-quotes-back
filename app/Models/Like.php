<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'user_id',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
}
