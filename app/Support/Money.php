<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class Money
{
    public static function cents(mixed $value): int
    {
        if (! is_scalar($value) || ! preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', (string) $value)) {
            throw ValidationException::withMessages(['paid_amount' => 'أدخل مبلغًا غير سالب بمنزلتين عشريتين كحد أقصى.']);
        }
        [$whole, $fraction] = array_pad(explode('.', (string) $value, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
