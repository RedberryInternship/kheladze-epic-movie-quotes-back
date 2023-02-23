<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'user_id',
        'type',
        'is_new',
        'writer_id'
    ];

    public function reciever()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }
}
