<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calcular subtotal del item
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    // Scope para obtener el carrito de un usuario
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}