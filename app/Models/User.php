<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        // Core identity
        'first_name', 'last_name', 'middle_name', 'suffix',
        'email', 'phone', 'password',
        // Account control
        'status', 'role',
        // Personal
        'gender', 'birthday', 'age', 'civil_status', 'spouse_name',
        'religion', 'home_municipality', 'home_barangay', 'permanent_address',
        // Contact & social
        'facebook_account', 'preferred_contact', 'profile_picture',
        'location', 'workplace', 'facebook_link', 'instagram_link', 'linkedin_link',
        // Academic
        'alumni_status', 'level', 'program', 'graduation_year', 'highest_degree',
        'honors', 'board_exam', 'other_schools',
        // Employment
        'present_occupation', 'other_experiences', 'company_address',
        // College-specific
        'academic_performance', 'employment_status', 'employment_type',
        'unemployment_reason', 'time_to_first_job', 'job_related', 'changes_needed',
        // Misc
        'comments', 'consent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'        => 'hashed',
            'birthday'        => 'date',
            'graduation_year' => 'integer',
            'age'             => 'integer',
        ];
    }

    // ── Computed helpers ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAvatarUrlAttribute(): string
    {
        if (! $this->profile_picture) {
            return asset('images/default-avatar.svg');
        }
        // External URL (e.g. https://picsum.photos/... stored by seeder or OAuth)
        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }
        return asset('storage/' . ltrim($this->profile_picture, '/'));
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    // ── Relationships ─────────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function postReactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function newsItems()
    {
        return $this->hasMany(News::class, 'uploaded_by');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'uploaded_by');
    }

    /** Connections I initiated */
    public function sentConnections()
    {
        return $this->hasMany(Connection::class, 'follower_id');
    }

    /** Connections others initiated with me */
    public function receivedConnections()
    {
        return $this->hasMany(Connection::class, 'followed_id');
    }

    /**
     * All connections (sent + received) as a single Eloquent query builder.
     * Usage: $user->connections()->accepted()->get()
     */
    public function connections()
    {
        return Connection::where(function ($q) {
            $q->where('follower_id', $this->id)
              ->orWhere('followed_id', $this->id);
        });
    }

    public function pendingRequests()
    {
        return $this->hasMany(PendingRequest::class, 'followed_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('joined_at');
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }
}
