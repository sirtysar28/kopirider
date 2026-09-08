<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'type' => ['required', 'in:deposit,balance,full'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'in:midtrans,bank_transfer'],
            'payment_link' => ['nullable', 'url', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        // Midtrans records can only be created while the integration is enabled.
        if ($data['method'] === 'midtrans' && ! MidtransService::enabled()) {
            return back()->with('error', 'Midtrans is disabled — enable it first in Settings → Payments, or choose bank transfer.');
        }

        $payment = $lead->payments()->create($data + [
            'currency' => setting('currency', 'IDR'),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payment request '.$payment->type.' created for '.$lead->reference.'.');
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,expired'],
            'payment_link' => ['nullable', 'url', 'max:500'],
        ]);

        $payment->update([
            'status' => $data['status'],
            'payment_link' => $data['payment_link'] ?: $payment->payment_link,
            'paid_at' => $data['status'] === 'paid' ? ($payment->paid_at ?? now()) : null,
        ]);

        // Deposit (or full payment) marked paid by hand → confirm booking + lock the date.
        if ($data['status'] === 'paid' && in_array($payment->type, ['deposit', 'full'])) {
            $lead = $payment->lead;
            $lead->update(['status' => 'confirmed']);

            if ($lead->event_date) {
                \App\Models\DateStatus::updateOrCreate(
                    ['date' => $lead->event_date->format('Y-m-d')],
                    ['status' => 'booked', 'note' => 'Booked — '.$lead->reference.' (deposit paid)'],
                );
            }
        }

        return back()->with('success', 'Payment status updated.');
    }

    /**
     * Generate (or regenerate) a Midtrans payment link for a pending payment.
     */
    public function generateLink(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Links can only be generated for pending payments.');
        }

        try {
            $url = MidtransService::createPaymentLink($payment);

            return back()->with('success', 'Midtrans link generated: '.$url);
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Midtrans: '.$e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Payment record deleted.');
    }
}
