<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index()
    {
        // Admin tüm yazıları görebilir
        $posts = Post::with('user')->get();
        return response()->json($posts);
    }
    // Yazarın kendi yazılarını getir
    public function authorPosts(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 6);

            // N+1 sorgusunu önlemek için sadece gerekli alanları çekiyoruz
            $posts = Post::with('categories:id,name')
                ->where('user_id', $request->user()->id)
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Yazılar yüklenirken hata oluştu.'], 500);
        }
    }


    public function publishedPosts()
    {
        try {
            // Sadece 'published' statüsündeki yazılar
            $posts = Post::with('categories') // kategorileri alıyoruz
            ->where('status', 'published')
                ->get()
                ->map(function($post) {
                    return [
                        'id' => $post->id,
                        'user_id' => $post->user_id,
                        'title' => $post->title,
                        'user_first_name' => $post->user ? $post->user->first_name : 'Bilinmiyor',
                        'user_last_name' => $post->user ? $post->user->last_name : 'Bilinmiyor',
                        'categories' => $post->categories->pluck('name'),
                        'published_at' => $post->published_at,
                        'content' => $post->content,
                    ];
                });

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Yeni yazı oluştur
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icerik' => 'required|string',
            'category_ids' => 'required|array',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        try {
            $post = new Post();
            $post->user_id = $request->user()->id;
            $post->title = $request->title;
            $post->slug = Str::slug($request->title) . '-' . uniqid();
            $post->content = $request->icerik;
            $post->status = $request->status;
            $post->published_at = $request->published_at ?? null;

            if ($request->hasFile('cover_image')) {
                $post->cover_image = $request->file('cover_image')->store('covers', 'public');
            }

            $post->save();
            $post->categories()->sync($request->category_ids);

            return response()->json($post, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Yazıyı güncelle
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icerik' => 'required|string',
            'category_ids' => 'required|array',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        try {
            $post->title = $request->title;
            $post->slug = Str::slug($request->title) . '-' . uniqid();
            $post->content = $request->icerik;
            $post->status = $request->status;
            $post->published_at = $request->published_at ?? null;

            if ($request->hasFile('cover_image')) {
                $post->cover_image = $request->file('cover_image')->store('covers', 'public');
            }

            $post->save();
            $post->categories()->sync($request->category_ids);

            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Yazı sil (soft delete)
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return response()->json(['message' => 'Yazı silindi.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Yazı silinirken hata oluştu.'], 500);
        }
    }

    // Yayın durumunu değiştir
    public function toggleStatus(Post $post)
    {
        try {
            $post->status = $post->status === 'published' ? 'draft' : 'published';
            $post->save();
            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Yayın durumu değiştirilemedi.'], 500);
        }
    }
    // Admin: tüm yazıları yazar bilgisi ile getir
    public function adminPosts()
    {
        try {
            $posts = Post::with('user')
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'user_first_name' => $post->user?->first_name ?? 'Bilinmiyor',
                        'user_last_name' => $post->user?->last_name ?? 'Bilinmiyor',
                        'status' => $post->status === 'published' ? 'Yayında' : 'Taslak',
                        'published_at' => $post->published_at,
                    ];
                });

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

}
