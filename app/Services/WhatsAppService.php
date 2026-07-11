<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;

    public function __construct()
    {
        $serverUrl = rtrim(config('services.whatsapp.url', 'http://127.0.0.1:3000'), '/');
        $this->baseUrl = str_ends_with($serverUrl, '/api') ? $serverUrl : "{$serverUrl}/api";
    }

    /**
     * Get the live status of the Node.js WhatsApp microservice.
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/status");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning('WhatsApp microservice unreachable: '.$e->getMessage());
        }

        return [
            'success' => false,
            'status' => 'disconnected',
            'error' => 'خادم الواتساب (Node.js Microservice) متوقف أو غير مشغل.',
        ];
    }

    /**
     * Send a WhatsApp text message.
     */
    public function sendMessage(string $number, string $message): bool
    {
        if (! SystemSetting::isWhatsAppEnabled()) {
            Log::info('WhatsApp message skipped because WhatsApp service is globally disabled.');

            return false;
        }

        try {
            $response = Http::timeout(8)->post("{$this->baseUrl}/send", [
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful() && ($response->json('success') ?? false)) {
                return true;
            }

            Log::error('WhatsApp API sending error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('WhatsApp API sending exception: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Logout and reset WhatsApp session.
     */
    public function logout(): bool
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/logout");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
