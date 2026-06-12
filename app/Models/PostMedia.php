<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostMedia extends Model
{
    protected $table    = 'post_media';
    protected $fillable = ['post_id', 'media_path', 'media_type'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->media_path, 'http')) {
            return $this->media_path;
        }
        return Storage::url($this->media_path);
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === 'video';
    }
}
