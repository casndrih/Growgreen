<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FoodProduct extends Model
{
    protected $fillable = ['vendor_id', 'category_id', 'name', 'description', 'price', 'image', 'stock', 'unit'];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(GroceryCategory::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }
}
