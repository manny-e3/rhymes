<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'title',
        'message',
        'icon',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    // Make sure formatted_data is included in JSON responses
    protected $appends = ['formatted_data'];

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('notifiable_type', User::class)
                    ->where('notifiable_id', $userId);
    }

    public function getFormattedDataAttribute()
    {
        $data = $this->data ?? [];
        return [
            'title' => $this->title ?? ($data['title'] ?? 'Notification'),
            'message' => $this->message ?? ($data['message'] ?? ($data['text'] ?? '')),
            'icon' => $this->icon ?? ($data['icon'] ?? 'ni ni-bell'),
            'type' => $this->getNotificationType(),
            'time' => $this->created_at ? $this->created_at->diffForHumans() : '',
        ];
    }

    private function getNotificationType()
    {
        $typeMap = [
            'App\Notifications\BookPublished' => 'success',
            'App\Notifications\BookSold' => 'info',
            'App\Notifications\PayoutProcessed' => 'success',
            'App\Notifications\SystemAlert' => 'warning',
            'App\Notifications\BookSubmitted' => 'info',
            'App\Notifications\BookStatusChanged' => 'info',
            'App\Notifications\PayoutRequested' => 'warning',
            'App\Notifications\PayoutStatusChanged' => 'info',
        ];

        return $typeMap[$this->type] ?? 'info';
    }

    // Ensure formatted_data is always serialized
    public function toArray()
    {
        $array = parent::toArray();
        $array['formatted_data'] = $this->formatted_data;
        return $array;
    }
}