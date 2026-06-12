<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventComment;
use App\Models\NewsComment;
use App\Models\Post;
use App\Models\PostComment;

class ModerationController extends Controller
{
    /** DELETE /admin/posts/{post} */
    public function deletePost(Post $post)
    {
        // Clean up stored media files
        foreach ($post->media as $media) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->media_path);
        }
        $post->delete();

        return back()->with('success', 'Post has been removed.');
    }

    /** DELETE /admin/post-comments/{comment} */
    public function deleteComment(PostComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment has been removed.');
    }

    /** DELETE /admin/event-comments/{comment} */
    public function deleteEventComment(EventComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Event comment has been removed.');
    }

    /** DELETE /admin/news-comments/{comment} */
    public function deleteNewsComment(NewsComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'News comment has been removed.');
    }
}
