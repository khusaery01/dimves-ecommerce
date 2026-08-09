<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected bool $isProduction;
    protected string $snapUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production', false);

        $this->snapUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Buat Snap Transaction token untuk order
     */
    public function createSnapTransaction(Order $order): array
    {
        $authHeader = 'Basic ' . base64_encode($this->serverKey . ':');

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id'       => (string) $item->menu_id,
                'price'    => (int) round($item->price),
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->menu->name ?? 'Menu Item', 0, 50),
            ];
        }

        // Jika ada ongkir
        if ($order->delivery_fee > 0) {
            $items[] = [
                'id'       => 'DELIVERY',
                'price'    => (int) round($order->delivery_fee),
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        // Jika ada diskon
        if ($order->discount_amount > 0) {
            $items[] = [
                'id'       => 'DISCOUNT',
                'price'    => (int) -round($order->discount_amount),
                'quantity' => 1,
                'name'     => 'Diskon Promo (' . ($order->voucher_code ?? 'Voucher') . ')',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => $order->order_code . '-' . time(),
                'gross_amount' => (int) round($order->grand_total > 0 ? $order->grand_total : $order->total_price),
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->user->name ?? 'Customer',
                'email'      => $order->user->email ?? 'customer@dimves.com',
                'phone'      => $order->user->phone ?? '08123456789',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => $authHeader,
            ])->post($this->snapUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Simpan transaksi di database payments
                $payment = Payment::create([
                    'order_id'       => $order->id,
                    'transaction_id' => $payload['transaction_details']['order_id'],
                    'snap_token'     => $data['token'] ?? null,
                    'snap_url'       => $data['redirect_url'] ?? null,
                    'amount'         => $payload['transaction_details']['gross_amount'],
                    'status'         => 'pending',
                    'raw_response'   => $data,
                ]);

                return [
                    'success'    => true,
                    'snap_token' => $data['token'] ?? null,
                    'snap_url'   => $data['redirect_url'] ?? null,
                    'payment'    => $payment,
                ];
            }

            Log::error('Midtrans API Error', ['body' => $response->body()]);
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Midtrans: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
