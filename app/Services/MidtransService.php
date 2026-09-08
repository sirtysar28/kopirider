<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Midtrans Payment Link integration.
 *
 * Enabled/disabled from Admin → Settings (midtrans_enabled) together with
 * the merchant credentials. When disabled the admin panel falls back to
 * plain bank transfer records and no API calls are ever made.
 */
class MidtransService
{
    public static function enabled(): bool
    {
        return setting('midtrans_enabled') === '1'
            && trim((string) setting('midtrans_server_key')) !== '';
    }

    public static function baseUrl(): string
    {
        return setting('midtrans_environment', 'sandbox') === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Create a shareable Midtrans payment link for a payment record.
     * Returns the hosted payment URL.
     *
     * @throws \RuntimeException on disabled integration or API failure
     */
    public static function createPaymentLink(Payment $payment): string
    {
        if (! self::enabled()) {
            throw new \RuntimeException('Midtrans is disabled or the server key is missing. Enable it first in Settings → Payments.');
        }

        $lead = $payment->lead;
        $orderId = self::buildOrderId($payment);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'item_details' => [[
                'id' => 'kr-'.$payment->type,
                'price' => (int) round((float) $payment->amount),
                'quantity' => 1,
                'name' => 'Kopi Rider '.ucfirst($payment->type).' ('.$lead->reference.')',
            ]],
            'customer_details' => [
                'first_name' => $lead->name ?? 'Customer',
                'phone' => $lead->whatsapp,
            ],
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode(setting('midtrans_server_key').':'),
        ])->post(self::baseUrl().'/v1/payment-links', $payload);

        if (! $response->successful()) {
            $message = $response->json('error_messages.0')
                ?? $response->json('message')
                ?? 'Midtrans API error (HTTP '.$response->status().')';
            Log::warning('Midtrans payment-link failed', ['status' => $response->status(), 'body' => $response->json()]);
            throw new \RuntimeException($message);
        }

        $url = $response->json('payment_url');
        if (! $url) {
            throw new \RuntimeException('Midtrans did not return a payment URL.');
        }

        $payment->forceFill([
            'midtrans_order_id' => $orderId,
            'payment_link' => $url,
        ])->save();

        return $url;
    }

    /**
     * Verify a Midtrans HTTP notification signature (sha512 of
     * order_id + status_code + gross_amount + server_key).
     */
    public static function verifySignature(?string $orderId, ?string $statusCode, ?string $grossAmount, ?string $signatureKey): bool
    {
        if (! $orderId || ! $signatureKey || ! setting('midtrans_server_key')) {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.setting('midtrans_server_key'));

        return hash_equals($expected, $signatureKey);
    }

    public static function buildOrderId(Payment $payment): string
    {
        return sprintf(
            '%s-%s-%d-%s',
            $payment->lead->reference,
            strtoupper(substr($payment->type, 0, 3)),
            $payment->id,
            strtolower(Str::random(4))
        );
    }
}
