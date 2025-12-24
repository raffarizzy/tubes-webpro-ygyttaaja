<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xendit\Xendit;
use Xendit\Invoice;

class CheckoutController extends Controller
{
    public function pay(Request $request)
    {
        try {
            Xendit::setApiKey(config('services.xendit.secret'));

            $invoice = Invoice::create([
                'external_id' => 'order-' . time(),
                'amount' => $request->total,
                'payer_email' => 'customer@example.com',
                'description' => 'Pembayaran SpareHub',
                'success_redirect_url' => url('/'),
                'failure_redirect_url' => url('/checkout'),
            ]);

            return response()->json([
                'invoice_url' => $invoice['invoice_url']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
