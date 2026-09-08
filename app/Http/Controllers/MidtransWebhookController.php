<?php

namespace App\Http\Controllers;

use App\Models\DateStatus;
use App\Models\Lead;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Midtrans HTTP notification (webhook) endpoint.
 *
 * Configure in Midtrans dashboard → Settings → Configuration:
 *   Payment Notification URL:  https://your-domain/api/midtrans/notification
 *
 * On a successful settlement the payment is marked paid automatically and,
 * for deposits, the lead becomes confirmed and its date is locked as booked
 * (the human confirmation rule is preserved — money actually arrived).
 */
class MidtransWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $data = $request->all();

        if (! MidtransService::verifySignature(
            $data['order_id'] ?? null,
            $data['status_code'] ?? null,
            $data['gross_amount'] ?? null,
            $data['signature_key'] ?? null,
        )) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $data['order_id'] ?? null]);

            return response()->json(['ok' => false], 401);
        }

        $payment = Payment::where('midtrans_order_id', $data['order_id'] ?? '')->first();

        if (! $payment) {
            return response()->json(['ok' => false, 'message' => 'unknown order'], 404);
        }

        $status = match ($data['transaction_status'] ?? '') {
            'settlement', 'capture' => 'paid',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny' => 'failed',
            default => null,
        };

        if ($status === null || $status === $payment->status) {
            return response()->json(['ok' => true, 'message' => 'no change']);
        }

        $payment->update([
            'status' => $status,
            'paid_at' => $status === 'paid' ? ($payment->paid_at ?? now()) : null,
        ]);

        // Deposit (or full payment) received → booking confirmed, date locked.
        if ($status === 'paid' && in_array($payment->type, ['deposit', 'full'])) {
            $lead = $payment->lead;
            $lead->update(['status' => 'confirmed']);

            if ($lead->event_date) {
                DateStatus::updateOrCreate(
                    ['date' => $lead->event_date->format('Y-m-d')],
                    ['status' => 'booked', 'note' => 'Booked — '.$lead->reference.' (deposit paid)'],
                );
            }
        }

        Log::info('Midtrans webhook processed', [
            'order_id' => $data['order_id'],
            'transaction_status' => $data['transaction_status'] ?? null,
            'payment' => $payment->id,
        ]);

        return response()->json(['ok' => true]);
    }
}
