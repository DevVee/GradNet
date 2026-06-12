<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'image_path', 'is_public'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->with('user')->latest();
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class)->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /** Reaction count by type, e.g. ['love' => 3, 'like' => 1] */
    public function reactionCounts(): array
    {
        return $this->reactions()
            ->selectRaw('reaction_type, count(*) as total')
            ->groupBy('reaction_type')
            ->pluck('total', 'reaction_type')
            ->toArray();
    }
}
