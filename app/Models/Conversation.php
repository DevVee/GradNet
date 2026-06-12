<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user1_id', 'user2_id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('sent_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany('sent_at');
    }

    /** Return the other participant relative to a given user */
    public function otherUser(int $myId): User
    {
        return $this->user1_id === $myId ? $this->user2 : $this->user1;
    }
}
