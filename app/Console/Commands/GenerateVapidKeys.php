<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature   = 'alumni:vapid-generate';
    protected $description = 'Generate VAPID public/private key pair for Web Push and write to .env';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->updateEnv('VAPID_PUBLIC_KEY',  $keys['publicKey']);
        $this->updateEnv('VAPID_PRIVATE_KEY', $keys['privateKey']);

        $this->info('VAPID keys generated and saved to .env:');
        $this->line("  VAPID_PUBLIC_KEY  = {$keys['publicKey']}");
        $this->line("  VAPID_PRIVATE_KEY = {$keys['privateKey']}");
        $this->warn('Run `php artisan config:clear` to reload the cached config.');

        return self::SUCCESS;
    }

    private function updateEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        if (str_contains($content, "{$key}=")) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $content);
    }
}
