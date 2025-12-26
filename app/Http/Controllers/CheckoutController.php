<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Proses pembayaran dengan Xendit
     * Menerima order_id yang sudah dibuat dari OrderController
     * 
     * ⚠️ PERHATIAN: Status langsung jadi "paid" tanpa webhook
     * Ini untuk development/testing - production seharusnya pakai webhook
     */

    public function index()
    {
        return view('checkout');
    }


    public function pay(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'total' => 'required|integer|min:1',
        ]);

        try {
            // Pastikan order milik user yang login
            $order = Order::where('id', $request->order_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Set Xendit API key
            \Xendit\Xendit::setApiKey(config('services.xendit.secret'));

            // Create Xendit invoice
            $invoice = \Xendit\Invoice::create([
                'external_id' => 'ORDER-' . $order->id . '-' . time(),
                'payer_email' => Auth::user()->email,
                'description' => 'Pembayaran Order #' . $order->id,
                'amount' => $request->total,
                'success_redirect_url' => route('payment.success'),
                'failure_redirect_url' => route('checkout'),
            ]);

            // 🔥 LANGSUNG UPDATE STATUS JADI PAID
            // (Gunakan status 'paid' sesuai ENUM database)
            $order->update([
                'invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
                'status' => 'paid', // ← Status langsung 'paid'
            ]);

            \Log::info('Order auto-completed', [
                'order_id' => $order->id,
                'invoice_id' => $invoice['id'],
                'status' => 'paid'
            ]);

            return response()->json([
                'message' => 'Invoice berhasil dibuat',
                'invoice_url' => $invoice['invoice_url'],
                'order_id' => $order->id,
                'status' => 'paid',
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment error:', [
                'message' => $e->getMessage(),
                'order_id' => $request->order_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Gagal membuat invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}