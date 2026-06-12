<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRequest extends Model
{
    public $timestamps = false;

    protected $fillable = ['follower_id', 'followed_id'];

    protected $casts = ['created_at' => 'datetime'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
