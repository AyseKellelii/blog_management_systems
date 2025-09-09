<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    // Belirli bir yazının yorumlarını getir
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user:id,first_name,last_name')
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    // Yorum oluştur
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'icerik' => 'required|string',
        ]);

        try {
            $comment = new Comment();
            $comment->post_id = $post->id;
            $comment->user_id = $request->user()->id;
            $comment->content = $request->icerik;
            $comment->approved = $request->user()->role === 'admin' ? true : false;
            $comment->save();

            return response()->json($comment, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // Kullanıcının kendi yorumlarını listele
    public function myComments(Request $request)
    {
        $comments = Comment::with(['post:id,title', 'user:id,first_name,last_name'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

// Kullanıcının kendi yorumunu güncelle
    public function update(Request $request, Comment $comment)
    {
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'Yetkisiz işlem'], 403);
        }

        $request->validate([
            'icerik' => 'required|string',
        ]);

        try {
            $comment->content = $request->icerik;
            $comment->approved = false; // Düzenleme sonrası tekrar admin onayı gerekebilir
            $comment->save();

            return response()->json([
                'message' => 'Yorum güncellendi, admin onayı bekliyor.',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

// Kullanıcının kendi yorumunu soft delete ile sil
    public function deleteOwn(Comment $comment, Request $request)
    {
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'Yetkisiz işlem'], 403);
        }

        try {
            $comment->delete(); // soft delete (Comment modelinde SoftDeletes olmalı!)
            return response()->json(['message' => 'Yorum silindi.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Tek yazı ve onun yorumlarını döndür
    public function singlePostWithComments(Post $post, Request $request)
    {
        try {
            if ($post->status !== 'published') {
                return response()->json(['error' => 'Yazı bulunamadı veya yayınlanmamış.'], 404);
            }

            $comments = $post->comments()
                ->with('user:id,first_name,last_name')
                ->where(function($query) use ($request) {
                    $query->where('approved', true)
                        ->orWhere('user_id', $request->user()->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $postData = [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user_first_name' => $post->user ? $post->user->first_name : 'Bilinmiyor',
                'user_last_name' => $post->user ? $post->user->last_name : 'Bilinmiyor',
                'categories' => $post->categories->pluck('name'),
                'published_at' => $post->published_at,
                'comments' => $comments
            ];

            return response()->json($postData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }



    // Admin: yorum onayla
    public function approve(Comment $comment)
    {
        $this->authorize('approve', $comment);
        $comment->approved = true;
        $comment->save();

        return response()->json($comment);
    }

    // Yorum sil (soft delete)
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json(['message' => 'Yorum silindi.']);
    }
}
