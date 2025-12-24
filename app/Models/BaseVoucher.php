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
        'disc_amt',
        'points_required',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
