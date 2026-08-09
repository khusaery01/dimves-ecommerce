<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Cek dan validasi kode voucher
     */
    public function check(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'order_total'  => 'required|numeric|min:0',
        ]);

        try {
            $promo = Promo::where('voucher_code', strtoupper($request->voucher_code))
                ->first();

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak ditemukan',
                ], 444);
            }

            if (!$promo->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher sudah tidak berlaku / kuota habis',
                ], 400);
            }

            if ($request->order_total < $promo->min_order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum pembelian untuk voucher ini adalah Rp ' . number_format($promo->min_order, 0, ',', '.'),
                ], 400);
            }

            $discountAmount = $promo->calculateDiscount((float) $request->order_total);

            return response()->json([
                'success'         => true,
                'message'         => 'Voucher berhasil dipasang!',
                'promo'           => $promo,
                'discount_amount' => $discountAmount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
