<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(FoodProduct::class);
    }

    public function close()
    {
        DB::transaction(function () {
            OrderHistory::create([
                'user_id' => $this->user_id,
                'vendor_id' => $this->vendor_id,
                'product_name' => $this->product_name,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'status' => 'closed',
            ]);

            $this->delete();
        });
    }

    protected static function booted(): void
    {
        static::updated(function (Order $order) {
            if ($order->status === 'closed' && $order->wasChanged('status')) {
                $order->close();
            }
        });
    }
}
