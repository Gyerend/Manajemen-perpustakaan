<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
    ];

    // FIX: Pastikan properti tanggal di-cast ke 'date' atau 'datetime'
    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
