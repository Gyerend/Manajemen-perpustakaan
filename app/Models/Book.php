<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'publisher', 'publication_year', 'category',
        'stock', 'max_loan_days', 'daily_fine_rate', 'description', 'image'
    ];

    // Relasi ke Reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke Loans
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
