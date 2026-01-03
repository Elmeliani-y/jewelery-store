<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'failed_attempts',
        'blocked_at',
        'last_attempt_at',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];

    /**
     * Check if IP is blocked
     */
    public static function isBlocked($ipAddress)
    {
        $record = self::where('ip_address', $ipAddress)->first();
        
        if (!$record || !$record->blocked_at) {
            return false;
        }
        
        return true;
    }

    /**
     * Record a failed attempt
     */
    public static function recordFailedAttempt($ipAddress)
    {
        $record = self::firstOrCreate(
            ['ip_address' => $ipAddress],
            ['failed_attempts' => 0]
        );

        $record->increment('failed_attempts');
        $record->last_attempt_at = now();

        // Block after 3 failed attempts
        if ($record->failed_attempts >= 3 && !$record->blocked_at) {
            $record->blocked_at = now();
        }

        $record->save();

        return $record;
    }

    /**
     * Reset failed attempts for an IP
     */
    public static function resetAttempts($ipAddress)
    {
        self::where('ip_address', $ipAddress)->update([
            'failed_attempts' => 0,
            'blocked_at' => null,
        ]);
    }
}
