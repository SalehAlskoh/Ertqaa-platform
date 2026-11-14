<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = ['application_id', 'status', 'enrolled_at', 'withdrawn_at'];

    protected function casts(): array
    {
        return ['enrolled_at' => 'datetime', 'withdrawn_at' => 'datetime', 'paid_at' => 'datetime', 'paid_amount' => 'decimal:2'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(EnrollmentApplication::class, 'application_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }
}
