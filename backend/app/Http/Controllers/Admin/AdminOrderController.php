<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Kitchen Display System (KDS) & Order Monitor
     */
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items.menu', 'items.variants'])
            ->latest()
            ->paginate(15);

        // Untuk AJAX polling di layar dapur
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'orders'  => $orders,
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Update status dapur & status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'         => 'nullable|string',
            'kitchen_status' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        if ($request->filled('kitchen_status')) {
            $order->kitchen_status = $request->kitchen_status;

            // Auto sync status utama jika kitchen status berubah
            if ($request->kitchen_status === 'preparing') {
                $order->status = 'Diproses';
            } else if ($request->kitchen_status === 'served') {
                $order->status = 'Selesai';
            }
        }

        if ($request->filled('status')) {
            $order->status = $request->status;
        }

        $order->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui',
                'order'   => $order,
            ]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
