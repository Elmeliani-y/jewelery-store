<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlockedIp;

class UnblockIp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ip:unblock {ip? : The IP address to unblock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unblock an IP address or list all blocked IPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ip = $this->argument('ip');

        if (!$ip) {
            // List all blocked IPs
            $blockedIps = BlockedIp::whereNotNull('blocked_at')->get();

            if ($blockedIps->isEmpty()) {
                $this->info('No blocked IPs found.');
                return 0;
            }

            $this->table(
                ['IP Address', 'Failed Attempts', 'Blocked At', 'Last Attempt'],
                $blockedIps->map(function ($record) {
                    return [
                        $record->ip_address,
                        $record->failed_attempts,
                        $record->blocked_at->format('Y-m-d H:i:s'),
                        $record->last_attempt_at ? $record->last_attempt_at->format('Y-m-d H:i:s') : '-',
                    ];
                })
            );

            $this->line('');
            $this->info('To unblock an IP, run: php artisan ip:unblock <ip-address>');
            return 0;
        }

        // Unblock specific IP
        $record = BlockedIp::where('ip_address', $ip)->first();

        if (!$record) {
            $this->error("IP address {$ip} not found in records.");
            return 1;
        }

        if (!$record->blocked_at) {
            $this->info("IP address {$ip} is not currently blocked.");
            return 0;
        }

        BlockedIp::resetAttempts($ip);
        $this->info("Successfully unblocked IP address: {$ip}");
        return 0;
    }
}
