<?php

namespace App\Support;

use Illuminate\Support\Str;

class UniqueSlug
{
    public static function make(string $model, string $title, int $max, ?int $ignore = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'item-'.Str::lower(Str::random(8));
        }
        $base = Str::substr($base, 0, $max - 12);
        $slug = $base;
        $n = 1;
        while ($model::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '<>', $ignore))->exists()) {
            $slug = $base.'-'.++$n;
        }

        return $slug;
    }
}
