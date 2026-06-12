<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    /** GET /news */
    public function index()
    {
        $news = News::orderByDesc('created_at')->paginate(12);
        return view('news.index', compact('news'));
    }

    /** GET /news/{news} */
    public function show(News $news)
    {
        $news->load(['likes', 'comments.user:id,first_name,last_name,profile_picture']);

        $authUser  = Auth::user();
        $userLiked = $news->likes->where('user_id', $authUser->id)->isNotEmpty();
        $likeCount = $news->likes->count();

        return view('news.show', compact('news', 'authUser', 'userLiked', 'likeCount'));
    }

    /** POST /news/{news}/like */
    public function toggleLike(News $news)
    {
        $existing = NewsLike::where('news_id', $news->id)->where('user_id', Auth::id())->first();

        if ($existing) { $existing->delete(); $liked = false; }
        else { NewsLike::create(['news_id' => $news->id, 'user_id' => Auth::id()]); $liked = true; }

        $count = NewsLike::where('news_id', $news->id)->count();
        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    /** POST /news/{news}/comments */
    public function storeComment(Request $request, News $news)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = NewsComment::create([
            'news_id' => $news->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        $comment->load('user:id,first_name,last_name,profile_picture');

        if ($request->wantsJson()) {
            return response()->json([
                'id'         => $comment->id,
                'content'    => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
                'user'       => [
                    'name'        => $comment->user->full_name,
                    'avatar'      => $comment->user->avatar_url,
                    'profile_url' => route('profile.show', $comment->user->id),
                ],
            ]);
        }

        return back()->with('success', 'Comment added.');
    }

    /** DELETE /news/{news}/comments/{comment} */
    public function destroyComment(News $news, NewsComment $comment)
    {
        abort_if($comment->user_id !== Auth::id(), 403);
        $comment->delete();
        if (request()->wantsJson()) return response()->json(['deleted' => true]);
        return back();
    }
}
