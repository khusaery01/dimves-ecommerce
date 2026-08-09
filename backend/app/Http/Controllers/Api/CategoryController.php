<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all active categories with menu count
     */
    public function index()
    {
        try {
            $categories = Category::withCount(['menus' => function ($query) {
                $query->where('status', true);
            }])->get();

            return response()->json([
                'success'    => true,
                'message'    => 'Daftar kategori berhasil diambil',
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori: ' . $e->getMessage(),
            ], 500);
        }
    }
}