<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'amount',
        'reason',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relasi
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
