<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAdminLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate admin secret login link';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('');
        $this->info('📝 Current Admin Secret Link:');
        $this->line('');
        
        $appUrl = rtrim(env('APP_URL', 'http://localhost'), '/');
        $this->line("{$appUrl}/admin-secret/password");
        
        $this->line('');
        $this->warn('⚠️  Default secret is "password" - Change it for production!');
        $this->line('');
        
        if ($this->confirm('Generate a new admin secret?', false)) {
            $newSecret = \Illuminate\Support\Str::random(32);
            $newHash = hash('sha256', $newSecret);
            
            $this->line('');
            $this->info('✅ New Admin Secret Generated!');
            $this->line('');
            $this->line('1. Update DeviceController.php:');
            $this->line("   ADMIN_SECRET_HASH = '{$newHash}'");
            $this->line('');
            $this->line('2. Use this link:');
            $this->line("   {$appUrl}/admin-secret/{$newSecret}");
            $this->line('');
            $this->warn("⚠️  Save '{$newSecret}' securely - you'll need it for the link!");
            $this->line('');
        }

        return 0;
    }
}
