<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseVoucher extends Model
{
    /** @use HasFactory<\Database\Factories\BaseVoucherFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'discount_amount',
        'points_required',
    ];
}
