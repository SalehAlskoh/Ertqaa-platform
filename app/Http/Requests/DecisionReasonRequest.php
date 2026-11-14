<?php

namespace App\Http\Requests;

use App\Rules\FitsTextColumn;
use Illuminate\Foundation\Http\FormRequest;

class DecisionReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin', 'staff');
    }

    public function rules(): array
    {
        return [$this->route('application') ? 'rejection_reason' : 'change_reason' => ['required', 'string', 'max:65000', new FitsTextColumn]];
    }
}
