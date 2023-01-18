<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'primary',
        'user_id',
        'token',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
