<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'courier_code',
    ];
    
    /**
     * Find delivery by courier code and service
     */
    public static function findByCourierCode($courierCode)
    {
        return self::where('courier_code', $courierCode)->first();
    }
}
