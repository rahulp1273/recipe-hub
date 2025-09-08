<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'store_orders';
    
    protected $fillable = [
        'customer_id',
        'store_id',
        'store_product_id',
        'quantity',
        'total_price',
        'payment_method',
        'status',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'notes',
        'delivery_time'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'delivery_time' => 'datetime'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function storeProduct(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class);
    }
}
