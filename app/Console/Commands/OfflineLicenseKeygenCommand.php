<?php

namespace App\Console\Commands;

use App\Services\OfflineLicense\LicenseSigningKey;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Generates a company Ed25519 seed file for Offline license signing.
 * Prints only the public key and path — never echoes the private seed.
 */
class OfflineLicenseKeygenCommand extends Command
{
    protected $signature = 'clinora:offline-license-keygen
        {--key-id=lic-2026-01 : Public key_id embedded in Offline releases}
        {--out= : Absolute path for the private seed file (base64)}';

    protected $description = 'Generate an Offline license signing key pair (private seed file + public key)';

    public function handle(): int
    {
        $keyId = (string) $this->option('key-id');
        $out = (string) ($this->option('out') ?: storage_path('app/private/offline-license/'.$keyId.'.seed'));

        if ($keyId === '' || preg_match('/^[A-Za-z0-9._-]{1,64}$/', $keyId) !== 1) {
            $this->error('Invalid --key-id.');

            return self::FAILURE;
        }

        if (str_contains($out, base_path('public')) || str_ends_with($out, '.php')) {
            $this->error('Refuse to write the private key under the public web root or as PHP.');

            return self::FAILURE;
        }

        try {
            $result = LicenseSigningKey::generateToFile($out, $keyId);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Private seed written (mode 0600). Keep this file off git and backups of the web tree.');
        $this->line('Path: '.$result['private_key_path']);
        $this->line('key_id: '.$result['key_id']);
        $this->line('public_key: '.$result['public_key']);
        $this->newLine();
        $this->warn('Add to .env:');
        $this->line('CLINORA_OFFLINE_LICENSE_KEY_ID='.$result['key_id']);
        $this->line('CLINORA_OFFLINE_LICENSE_PRIVATE_KEY_PATH='.$result['private_key_path']);
        $this->line('CLINORA_OFFLINE_LICENSE_PRODUCTION=true');
        $this->newLine();
        $this->warn('Ship the same key_id + public_key into Clinora Offline config/clinora.php and Agent LicensePublicKeys.Trusted before clinics can import.');

        return self::SUCCESS;
    }
}
