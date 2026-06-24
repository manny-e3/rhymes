<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RevMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'rev_book_id',
        'sync_status',
        'last_synced_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}