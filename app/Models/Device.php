<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name', 'user_id', 'token', 'user_agent', 'last_login_at'
    ];
}
