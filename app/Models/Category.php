<?php

namespace App\Models;

use App\Models\Concerns\HasGuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasGuid;

    protected $fillable = ['guid', 'name', 'slug', 'status'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
