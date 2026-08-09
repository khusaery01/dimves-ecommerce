<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get list of active menus with category and variants
     */
    public function index(Request $request)
    {
        try {
            $query = Menu::with(['category', 'variants.options'])
                ->where('status', true);

            // Filter per kategori jika dikirim client
            if ($request->has('category_id') && $request->category_id != 0) {
                $query->where('category_id', $request->category_id);
            }

            // Pencarian nama menu
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            $menus = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar menu berhasil diambil',
                'menus'   => $menus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data menu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail menu spesifik berserta varian kustomisasi
     */
    public function show($id)
    {
        try {
            $menu = Menu::with(['category', 'variants.options'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'menu'    => $menu,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan',
            ], 404);
        }
    }
}