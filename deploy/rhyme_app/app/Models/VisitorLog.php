<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
         'location',
        'user_agent',
        'referer',
        'path',
        'visited_at',
        'user_id',
        'client_id',
        'session_id',
        'is_new_user',
        'request_data'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'request_data' => 'array',
        'is_new_user' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get new users
    public function scopeNewUsers($query)
    {
        return $query->where('is_new_user', true);
    }

    // Scope to get returning users
    public function scopeReturningUsers($query)
    {
        return $query->where('is_new_user', false);
    }

    // Scope to get sessions for a specific client
    public function scopeByClientId($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Scope to get all records for a specific session
    public function scopeBySessionId($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
} 