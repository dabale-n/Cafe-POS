<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'wallet_discount', 'requires_queue'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
