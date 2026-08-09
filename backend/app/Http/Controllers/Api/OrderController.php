<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuVariantOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemVariant;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // ================= CHECKOUT =================

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method'   => 'required|string',
            'shipping_address' => 'required_if:order_type,delivery',
            'order_type'       => 'nullable|in:dine_in,takeaway,delivery',
            'table_number'     => 'nullable|string',
            'voucher_code'     => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $itemsToCreate = [];

            // 1. Validasi Stok & Hitung Total Harga Item + Varian
            foreach ($request->items as $itemData) {
                $menu = Menu::find($itemData['menu_id']);

                if (!$menu || !$menu->status) {
                    throw new \Exception("Menu '{$menu->name}' sedang tidak tersedia.");
                }

                // Cek Stok (Auto Out-of-Stock)
                if ($menu->stock < $itemData['quantity']) {
                    throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi (Tersisa: {$menu->stock}).");
                }

                $itemBasePrice = (float) $menu->price;
                $extraVariantPrice = 0;
                $variantsToCreate = [];

                // Olah varian kustomisasi jika ada (Level pedas, topping, dll)
                if (!empty($itemData['variants']) && is_array($itemData['variants'])) {
                    foreach ($itemData['variants'] as $vData) {
                        $option = MenuVariantOption::with('variant')->find($vData['option_id']);
                        if ($option) {
                            $extraVariantPrice += (float) $option->extra_price;
                            $variantsToCreate[] = [
                                'menu_variant_id'        => $option->menu_variant_id,
                                'menu_variant_option_id' => $option->id,
                                'variant_name'           => $option->variant->name ?? 'Custom',
                                'option_name'            => $option->name,
                                'extra_price'            => $option->extra_price,
                            ];
                        }
                    }
                }

                $finalUnitPrice = $itemBasePrice + $extraVariantPrice;
                $itemSubtotal   = $finalUnitPrice * $itemData['quantity'];
                $totalPrice    += $itemSubtotal;

                $itemsToCreate[] = [
                    'menu'              => $menu,
                    'quantity'          => $itemData['quantity'],
                    'price'             => $finalUnitPrice,
                    'subtotal'          => $itemSubtotal,
                    'note'              => $itemData['note'] ?? null,
                    'variantsToCreate'  => $variantsToCreate,
                ];
            }

            // 2. Perhitungan Voucher / Promo
            $discountAmount = 0;
            $promoId = null;
            $voucherCode = null;

            if ($request->filled('voucher_code')) {
                $promo = Promo::where('voucher_code', strtoupper($request->voucher_code))->first();
                if ($promo && $promo->isValid() && $totalPrice >= $promo->min_order) {
                    $discountAmount = $promo->calculateDiscount($totalPrice);
                    $promoId = $promo->id;
                    $voucherCode = $promo->voucher_code;

                    // Increment kuota pemakaian promo
                    $promo->increment('used_count');
                }
            }

            // 3. Hitung Ongkir (Flat / Distance)
            $orderType = $request->order_type ?? 'delivery';
            $deliveryFee = ($orderType === 'delivery') ? (float) ($request->delivery_fee ?? 10000) : 0;

            $grandTotal = max(0, $totalPrice - $discountAmount + $deliveryFee);

            // Mapping payment_method
            $paymentMethod = $request->payment_method;
            if ($paymentMethod === 'COD') $paymentMethod = 'Cash';

            // 4. Create Order Record
            $order = Order::create([
                'user_id'          => $request->user()->id,
                'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
                'total_price'      => $totalPrice,
                'discount_amount'  => $discountAmount,
                'delivery_fee'     => $deliveryFee,
                'grand_total'      => $grandTotal,
                'payment_method'   => $paymentMethod,
                'shipping_address' => $request->shipping_address ?? '-',
                'order_type'       => $orderType,
                'table_number'     => $request->table_number,
                'kitchen_status'   => 'waiting',
                'status'           => 'Pending',
                'notes'            => $request->notes,
                'voucher_code'     => $voucherCode,
                'promo_id'         => $promoId,
            ]);

            // 5. Create Order Items & Potong Stok
            foreach ($itemsToCreate as $itemInfo) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $itemInfo['menu']->id,
                    'quantity' => $itemInfo['quantity'],
                    'price'    => $itemInfo['price'],
                    'subtotal' => $itemInfo['subtotal'],
                    'note'     => $itemInfo['note'],
                ]);

                // Simpan varian per item
                foreach ($itemInfo['variantsToCreate'] as $vInfo) {
                    OrderItemVariant::create(array_merge($vInfo, [
                        'order_item_id' => $orderItem->id,
                    ]));
                }

                // Potong stok menu
                $itemInfo['menu']->decrement('stock', $itemInfo['quantity']);

                // Auto set status offline jika stok habis
                if ($itemInfo['menu']->fresh()->stock <= 0) {
                    $itemInfo['menu']->update(['status' => false]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'order'   => $order->load(['items.menu', 'items.variants', 'promo']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // ================= LIST PESANAN =================

    public function index(Request $request)
    {
        try {
            $orders = Order::with(['items.menu', 'items.variants'])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'orders'  => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ================= DETAIL PESANAN =================

    public function show(Request $request, $id)
    {
        try {
            $order = Order::with(['items.menu', 'items.variants', 'latestPayment'])
                ->where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'order'   => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }
    }

    // ================= POLLING STATUS PESANAN (REAL-TIME) =================

    public function statusCheck(Request $request, $id)
    {
        try {
            $order = Order::select('id', 'order_code', 'status', 'kitchen_status', 'updated_at')
                ->where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return response()->json([
                'success'        => true,
                'status'         => $order->status,
                'kitchen_status' => $order->kitchen_status,
                'updated_at'     => $order->updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}