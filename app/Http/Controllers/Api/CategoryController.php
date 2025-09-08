<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Listeleme
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json($categories);
    }

    // Yeni kategori ekle
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($category);
    }

    // Tek kategori getir (opsiyonel)
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    // Kategori güncelle
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($category);
    }

    // Kategori sil (soft delete)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Kategori silindi.']);
    }
}
