<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    protected $fillable = ['follower_id', 'followed_id', 'status'];

    // Aliases so code can use "requester" / "recipient" terminology
    public function requester()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    // Keep original names too
    public function follower()  { return $this->requester(); }
    public function followed()  { return $this->recipient(); }

    public function getRequesterIdAttribute(): int  { return $this->follower_id; }
    public function getRecipientIdAttribute(): int   { return $this->followed_id; }

    public function scopeAccepted($query) { return $query->where('status', 'accepted'); }
    public function scopePending($query)  { return $query->where('status', 'pending'); }
}
