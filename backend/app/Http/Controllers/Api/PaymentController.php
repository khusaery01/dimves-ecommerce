<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Generate Snap Token untuk transaksi Midtrans
     */
    public function createPayment(Request $request, $orderId)
    {
        try {
            $order = Order::with(['items.menu', 'user'])
                ->where('id', $orderId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // Cek jika sudah ada payment pending yang aktif
            $existingPayment = Payment::where('order_id', $order->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($existingPayment && $existingPayment->snap_token) {
                return response()->json([
                    'success'    => true,
                    'snap_token' => $existingPayment->snap_token,
                    'snap_url'   => $existingPayment->snap_url,
                    'payment'    => $existingPayment,
                ]);
            }

            $result = $this->midtransService->createSnapTransaction($order);

            if ($result['success']) {
                return response()->json([
                    'success'    => true,
                    'snap_token' => $result['snap_token'],
                    'snap_url'   => $result['snap_url'],
                    'payment'    => $result['payment'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook Callback dari Midtrans setelah customer bayar
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received', $payload);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Cari data payment berdasarkan transaction_id / order_id string
        $payment = Payment::where('transaction_id', $orderId)->first();

        if (!$payment) {
            // Coba cari dari prefix order_code
            $orderCode = explode('-', $orderId)[0] ?? null;
            $order = Order::where('order_code', 'LIKE', $orderCode . '%')->first();

            if ($order) {
                $payment = Payment::where('order_id', $order->id)->latest()->first();
            }
        }

        if (!$payment) {
            return response()->json(['message' => 'Payment record not found'], 444);
        }

        $order = $payment->order;

        // Update status pembayaran berdasarkan status Midtrans
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $payment->status = 'pending';
            } else if ($fraudStatus == 'accept') {
                $payment->status = 'settlement';
                $payment->paid_at = now();
                if ($order) $order->update(['status' => 'Diproses', 'kitchen_status' => 'waiting']);
            }
        } else if ($transactionStatus == 'settlement') {
            $payment->status = 'settlement';
            $payment->paid_at = now();
            if ($order) $order->update(['status' => 'Diproses', 'kitchen_status' => 'waiting']);
        } else if ($transactionStatus == 'pending') {
            $payment->status = 'pending';
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $payment->status = $transactionStatus;
            if ($order) $order->update(['status' => 'Dibatalkan']);
        }

        $payment->payment_type = $paymentType;
        $payment->raw_response = $payload;
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification processed successfully',
        ]);
    }
}
