<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'method',
    ];
    
    /**
     * Get enabled payment channels based on payment method
     * 
     * @return array
     */
    public function getEnabledPayments(): array
    {
        // Match based on method name (case insensitive)
        $method = strtolower($this->method);
        
        if (str_contains($method, 'bank') || str_contains($method, 'transfer')) {
            return ['bca', 'bni', 'bri', 'mandiri', 'permata'];
        }
        
        if (str_contains($method, 'credit') || str_contains($method, 'card')) {
            return ['credit_card'];
        }
        
        if (str_contains($method, 'gopay') || str_contains($method, 'wallet') || str_contains($method, 'e-wallet')) {
            return ['gopay', 'shopeepay'];
        }
        
        if (str_contains($method, 'qris')) {
            return ['qris'];
        }
        
        if (str_contains($method, 'alfamart') || str_contains($method, 'indomaret') || str_contains($method, 'convenience') || str_contains($method, 'store')) {
            return ['alfamart', 'indomaret'];
        }
        
        // Default: return empty (show all payment methods)
        return [];
    }
    
    /**
     * Get payment type for Midtrans based on method
     * 
     * @return string|null
     */
    public function getPaymentType(): ?string
    {
        $method = strtolower($this->method);
        
        if (str_contains($method, 'bank') || str_contains($method, 'transfer')) {
            return 'bank_transfer';
        }
        
        if (str_contains($method, 'credit') || str_contains($method, 'card')) {
            return 'credit_card';
        }
        
        if (str_contains($method, 'gopay') || str_contains($method, 'wallet') || str_contains($method, 'e-wallet')) {
            return 'gopay';
        }
        
        if (str_contains($method, 'qris')) {
            return 'qris';
        }
        
        return null;
    }
    
    /**
     * Get icon class based on payment type
     * 
     * @return string
     */
    public function getIconColor(): string
    {
        $type = $this->getPaymentType();
        
        return match($type) {
            'bank_transfer' => 'text-blue-600',
            'credit_card' => 'text-purple-600',
            'gopay' => 'text-green-600',
            'qris' => 'text-indigo-600',
            'cstore' => 'text-orange-600',
            default => 'text-gray-600',
        };
    }
}
