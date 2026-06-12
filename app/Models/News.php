<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image_path', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function likes()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class)->with('user')->latest();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) return null;
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
