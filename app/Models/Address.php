<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'detail',
        'is_default',
        'user_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Set this address as default and unset others
    public function setAsDefault()
    {
        // Unset all other addresses as default for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this address as default
        $this->update(['is_default' => true]);
    }
}
