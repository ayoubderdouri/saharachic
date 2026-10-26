<?php

namespace App\Models;

use App\Models\Concerns\HasGuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasGuid;

    protected $fillable = ['guid', 'name', 'slug', 'hex_code'];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
