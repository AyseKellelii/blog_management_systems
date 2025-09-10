<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;


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

    // kullanıcı Yorum oluştur ve yazara bildirim gitme
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'icerik' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user()->id,
            'content' => $request->icerik,
            'approved' => false,
        ]);

        // Yazar
        $post = $comment->post;
        $author = $post->user;

        if ($author && $author->id !== $request->user()->id) {
            $author->notify(new NewCommentNotification($comment));
        }

        return response()->json($comment, 201);
    }

    // Kullanıcının kendi yorumları
    public function myComments()
    {
        $user = auth()->user();

        $comments = Comment::with('post')
            ->withTrashed() // silinmiş yorumlar dahil
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'approved' => $comment->approved,
                    'post_title' => $comment->post?->title ?? 'Bilinmiyor',
                    'created_at' => $comment->created_at,
                ];
            });

        return response()->json($comments);
    }
    public function authorComments(Request $request)
    {
        $user = $request->user();

        // Yazarın yazılarına gelen yorumları al
        $comments = Comment::with(['user', 'post'])
            ->whereHas('post', function($query) use ($user) {
                $query->where('user_id', $user->id); // sadece kendi yazıları
            })
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



    // Admin: tüm yorumları getir
    public function allComments()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }

        $comments = Comment::with(['user:id,first_name,last_name,role', 'post:id,title'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                // Admin yorumları direkt onaylı
                if (($comment->user->role ?? null) === 'admin') {
                    $comment->approved = true;
                }
                return $comment;
            });

        return response()->json($comments);
    }

// Admin: Bekleyen yorumları listele
    public function pendingComments()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }

        $comments = Comment::with(['user:id,first_name,last_name', 'post:id,title'])
            ->where('approved', false)
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['user', 'author']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

// Admin: Yorumu onayla
    public function approve($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }

        $comment = Comment::findOrFail($id);
        $comment->approved = true;
        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

// Admin: Yorumu sil
    public function adminDelete($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }

        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Yorum silindi']);
    }
}
