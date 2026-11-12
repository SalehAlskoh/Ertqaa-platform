<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = ['application_id', 'status', 'enrolled_at', 'withdrawn_at'];

    protected function casts(): array
    {
        return ['enrolled_at' => 'datetime', 'withdrawn_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(EnrollmentApplication::class, 'application_id');
    }
}
