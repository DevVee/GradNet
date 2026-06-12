<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image_path', 'event_datetime', 'location', 'uploaded_by'];

    protected function casts(): array
    {
        return ['event_datetime' => 'datetime'];
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function likes()
    {
        return $this->hasMany(EventLike::class);
    }

    public function comments()
    {
        return $this->hasMany(EventComment::class)->with('user')->latest();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) return null;
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    public function isUpcoming(): bool
    {
        return $this->event_datetime->isFuture();
    }

    // ── RSVP ─────────────────────────────────────────────────────────

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function goingCount(): int
    {
        return $this->rsvps()->where('status', 'going')->count();
    }

    public function maybeCount(): int
    {
        return $this->rsvps()->where('status', 'maybe')->count();
    }
}
