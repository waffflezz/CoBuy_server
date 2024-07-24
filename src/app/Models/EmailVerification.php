<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'pin_code', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
