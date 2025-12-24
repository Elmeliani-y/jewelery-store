<?php
namespace App\Policies;

use App\Models\User;

class DevicePolicy
{
    public function admin(User $user)
    {
        return $user->role === 'admin';
    }
}
