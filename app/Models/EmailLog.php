<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sent_by',
        'template_id',
        'subject',
        'content',
        'recipients',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'metadata',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'metadata' => 'array',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
