<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuVariantOption;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'variants.options'])->latest()->get();
        $categories = Category::all();
        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'image'       => $imagePath,
            'status'      => $request->stock > 0,
        ]);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    public function toggleStock(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->status = !$menu->status;
        $menu->save();

        return response()->json([
            'success' => true,
            'status'  => $menu->status,
            'message' => 'Status stok menu berhasil diperbarui',
        ]);
    }
}
