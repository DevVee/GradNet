<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostMedia;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  POSTS — CRUD
    // ─────────────────────────────────────────────────────────────────

    /** POST /posts */
    public function store(Request $request)
    {
        $request->validate([
            'content'  => 'required|string|max:5000',
            'is_public'=> 'sometimes|boolean',
            'media.*'  => 'file|max:51200|mimes:jpeg,jpg,png,gif,mp4,mov,avi',
        ]);

        $post = Post::create([
            'user_id'   => Auth::id(),
            'content'   => $request->content,
            'is_public' => $request->boolean('is_public', true),
        ]);

        $this->storeMedia($post->id, $request);

        return redirect()->route('feed.index')->with('success', 'Post created!');
    }

    /** GET /posts/{post} */
    public function show(Post $post)
    {
        $this->authorizeView($post);

        $post->load([
            'user:id,first_name,last_name,profile_picture',
            'media',
            'reactions.user:id,first_name,last_name',
            'comments.user:id,first_name,last_name,profile_picture',
            'comments.reactions',
        ]);

        return view('posts.show', ['post' => $post, 'authUser' => Auth::user()]);
    }

    /** PATCH /posts/{post} */
    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        $request->validate(['content' => 'required|string|max:5000']);

        $post->update([
            'content'   => $request->content,
            'is_public' => $request->boolean('is_public', $post->is_public),
        ]);

        $this->storeMedia($post->id, $request);

        return back()->with('success', 'Post updated!');
    }

    /** DELETE /posts/{post} */
    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        // Delete associated media files
        foreach ($post->media as $media) {
            if ($media->media_type !== 'url') Storage::delete($media->media_path);
        }
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    // ─────────────────────────────────────────────────────────────────
    //  REACTIONS
    // ─────────────────────────────────────────────────────────────────

    /** POST /posts/{post}/react */
    public function react(Post $post)
    {
        $this->authorizeView($post);

        $existing = PostReaction::where('post_id', $post->id)
            ->where('user_id', Auth::id())
            ->where('reaction_type', 'love')
            ->first();

        if ($existing) {
            $existing->delete();
            $reacted = false;
        } else {
            PostReaction::create([
                'post_id'       => $post->id,
                'user_id'       => Auth::id(),
                'reaction_type' => 'love',
            ]);
            $reacted = true;
        }

        $count = PostReaction::where('post_id', $post->id)->where('reaction_type', 'love')->count();

        return response()->json(['reacted' => $reacted, 'count' => $count]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  COMMENTS
    // ─────────────────────────────────────────────────────────────────

    /** POST /posts/{post}/comments */
    public function storeComment(Request $request, Post $post)
    {
        $this->authorizeView($post);
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = PostComment::create([
            'post_id' => $post->id,
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
                    'id'              => $comment->user->id,
                    'name'            => $comment->user->full_name,
                    'avatar'          => $comment->user->avatar_url,
                    'profile_url'     => route('profile.show', $comment->user->id),
                ],
            ]);
        }

        return back()->with('success', 'Comment added.');
    }

    /** DELETE /posts/{post}/comments/{comment} */
    public function destroyComment(Post $post, PostComment $comment)
    {
        abort_if($comment->user_id !== Auth::id() && $post->user_id !== Auth::id(), 403);
        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Comment removed.');
    }

    // ─────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────

    private function storeMedia(int $postId, Request $request): void
    {
        if (!$request->hasFile('media')) return;

        foreach ($request->file('media') as $file) {
            $path     = $file->store('posts', 'public');
            $mimeType = $file->getMimeType();
            $type     = str_starts_with($mimeType, 'video') ? 'video' : 'image';

            PostMedia::create([
                'post_id'    => $postId,
                'media_path' => $path,
                'media_type' => $type,
            ]);
        }
    }

    private function authorizeView(Post $post): void
    {
        $user = Auth::user();
        if ($post->user_id === $user->id) return;      // own post
        if ($post->is_public) return;                  // public post
        abort(403);
    }
}
