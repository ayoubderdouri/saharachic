<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasGuid
{
    protected static function bootHasGuid(): void
    {
        static::creating(function ($model) {
            $model->guid ??= (string) Str::uuid();
        });
    }
}
