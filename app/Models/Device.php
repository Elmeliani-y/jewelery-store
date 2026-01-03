<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name', 'user_id', 'token', 'last_login_at', 'active', 'first_used_at', 'first_used_ip'
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'first_used_at' => 'datetime',
    ];
}

