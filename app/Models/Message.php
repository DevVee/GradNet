<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conversation_id', 'group_id', 'sender_id',
        'content', 'content_type', 'sent_at', 'seen_at', 'reply_to_id',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'seen_at' => 'datetime',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id')->with('sender');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
