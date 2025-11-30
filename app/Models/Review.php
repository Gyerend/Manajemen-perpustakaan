<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    // PASTIKAN SEMUA FIELD INI ADA UNTUK MENGHILANGKAN MASSASSIGNMENTEXCEPTION
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    // ... (Relasi di bawah)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
