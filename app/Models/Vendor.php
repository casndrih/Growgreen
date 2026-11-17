<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Vendor extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'profile_picture', 'cover_picture',
        'address', 'withdrawal_type', 'withdrawal_details', 'status', 'reason',
        'verification', 'description',
    ];

    public function getProfilePictureUrlAttribute(): ?string
    {
        if (! $this->profile_picture) {
            return null;
        }

        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        return Storage::disk('public')->url($this->profile_picture);
    }

    public function isVendor()
    {
        return $this->role === 'vendor'; // Assuming 'role' column in vendors table holds the role information
    }

    public function products()
    {
        return $this->hasMany(FoodProduct::class);
    }
}
