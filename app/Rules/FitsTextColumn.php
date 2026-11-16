<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FitsTextColumn implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // TEXT is byte-limited; leave room for the notification's Arabic prefix.
        if (is_string($value) && strlen($value) > 65000) {
            $fail('النص طويل جدًا؛ اختصره ثم أعد المحاولة.');
        }
    }
}
