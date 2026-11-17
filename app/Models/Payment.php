<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'paypal_order_id',
        'paypal_capture_id',
        'amount',
        'currency',
        'status',
        'order_ids',
        'response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'order_ids' => 'array',
            'response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
