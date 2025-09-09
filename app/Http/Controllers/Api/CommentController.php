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
            $author->notify(new \App\Notifications\NewCommentNotification($comment));
        }

        return response()->json($comment, 201);
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
    // Admin: tüm yorumları getir
    public function allComments()
    {
        $comments = Comment::with(['user:id,first_name,last_name', 'post:id,title'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }
    // Admin: Bekleyen yorumları listele
    public function pendingComments()
    {
        $this->authorize('adminOnly', Comment::class);

        $comments = Comment::with(['user:id,first_name,last_name', 'post:id,title'])
            ->where('approved', false)
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'admin'); // Admin yorumları hariç
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

// Admin: Yorumu onayla
    // Admin: Yorumu onayla
    public function approve($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->approved = true; // ✅ doğru sütun
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'Yorum başarıyla onaylandı',
                'comment' => $comment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

// Admin: Yorumu soft delete ile sil
    public function adminDelete(Comment $comment)
    {
        $this->authorize('adminOnly', Comment::class);

        $comment->delete();
        return response()->json(['message' => 'Yorum silindi']);
    }

    // Author'ın yazılarına gelen yorumlar
    public function authorComments(Request $request)
    {
        $comments = Comment::with([
            'post:id,title',
            'post.categories:id,name', // kategorileri de getir
            'user:id,first_name,last_name'
        ])
            ->whereHas('post', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                $comment->is_approved = (bool) $comment->approved;
                return $comment;
            });

        return response()->json($comments);
    }

}
