<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageTestimony extends Model
{
    /** @use HasFactory<\Database\Factories\TestimoniesFactory> */
    use HasFactory;
    protected $fillable = [
        'testimony_id',
        'image_url',
    ];
    public function testimony()
    {
        return $this->belongsTo(Testimony::class);
    }
}
