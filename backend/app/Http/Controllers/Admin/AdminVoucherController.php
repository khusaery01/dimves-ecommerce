<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class AdminVoucherController extends Controller
{
    public function index()
    {
        $vouchers = Promo::latest()->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'voucher_code'   => 'required|string|max:50|unique:promos,voucher_code',
            'description'    => 'nullable|string',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order'      => 'nullable|numeric|min:0',
            'max_discount'   => 'nullable|numeric|min:0',
            'quota'          => 'nullable|integer|min:1',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        Promo::create([
            'name'           => $request->name,
            'voucher_code'   => strtoupper($request->voucher_code),
            'description'    => $request->description,
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order'      => $request->min_order ?? 0,
            'max_discount'   => $request->max_discount,
            'quota'          => $request->quota,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => true,
        ]);

        return redirect()->back()->with('success', 'Voucher berhasil ditambahkan!');
    }

    public function toggleActive($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();

        return redirect()->back()->with('success', 'Status voucher berhasil diubah!');
    }

    public function destroy($id)
    {
        Promo::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Voucher berhasil dihapus!');
    }
}
