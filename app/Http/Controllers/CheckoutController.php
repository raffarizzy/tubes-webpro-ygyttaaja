<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Alamat;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout dengan data dari database
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua alamat user
        $alamats = Alamat::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->get();
        
        // Ambil keranjang user dengan items
        $keranjang = Keranjang::with(['items.product'])
            ->where('user_id', $user->id)
            ->first();
        
        $cartItems = $keranjang ? $keranjang->items : collect([]);
        
        // Hitung total
        $subtotal = $cartItems->sum('subtotal');
        $deliveryFee = 0;
        $discount = 0;
        $total = $subtotal + $deliveryFee - $discount;
        
        return view('checkout', compact('alamats', 'cartItems', 'subtotal', 'deliveryFee', 'discount', 'total'));
    }

    /**
     * Proses pembayaran dengan Xendit (order sudah dibuat sebelumnya di /api/orders)
     */
    public function pay(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'alamat_id' => 'required|exists:alamats,id',
            'total' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            
            // Ambil order yang sudah dibuat sebelumnya
            $order = Order::with('items')
                ->where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();
            
            // Update alamat jika berbeda
            if ($order->alamat_id !== $request->alamat_id) {
                $order->update(['alamat_id' => $request->alamat_id]);
            }
            
            // Set Xendit API key
            \Xendit\Xendit::setApiKey(config('services.xendit.secret'));

            // Create Xendit invoice
            $invoice = \Xendit\Invoice::create([
                'external_id' => 'ORDER-' . $order->id . '-' . time(),
                'payer_email' => $user->email,
                'description' => 'Pembayaran Order #' . $order->id,
                'amount' => $request->total,
                'success_redirect_url' => route('payment.success'),
                'failure_redirect_url' => route('checkout'),
            ]);

            // Update order status ke paid (invoice data disimpan di session/cache jika perlu)
            $order->update([
                'status' => 'paid',
            ]);
            
            // OPTIONAL: Simpan invoice data ke session untuk tracking
            session([
                'last_invoice_id' => $invoice['id'],
                'last_invoice_url' => $invoice['invoice_url'],
            ]);
            
            DB::commit();

            \Log::info('Payment processed successfully', [
                'order_id' => $order->id,
                'invoice_id' => $invoice['id'],
                'status' => 'paid'
            ]);

            // Return JSON response untuk AJAX handling
            return response()->json([
                'message' => 'Payment berhasil diproses',
                'invoice_url' => $invoice['invoice_url'],
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Payment error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Callback setelah payment success
     */
    public function paymentSuccess()
    {
        return redirect()->route('riwayat.pesanan')->with('success', 'Pembayaran berhasil!');
    }
}