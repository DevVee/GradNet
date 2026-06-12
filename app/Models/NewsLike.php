<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['news_id', 'user_id'];

    protected $casts = ['created_at' => 'datetime'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
