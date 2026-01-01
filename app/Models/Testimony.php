<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    /** @use HasFactory<\Database\Factories\TestimoniesFactory> */
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'rating',
        'description',
    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
