<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['group_name', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('joined_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('sent_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany('sent_at');
    }
}
