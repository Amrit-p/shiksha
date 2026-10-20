<?php

namespace App\Http\Requests;

use App\Models\OrderPayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Payment entry form (salesman log/edit, admin record from order detail). Who-may-do-what lives in OrderPaymentService. */
class OrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:99999999',
            'paid_on' => 'required|date|before_or_equal:'.now('Asia/Kolkata')->toDateString(),
            'mode' => ['required', Rule::in(array_keys(OrderPayment::MODES))],
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'paid_on.before_or_equal' => 'The received date cannot be in the future.',
            'proof.max' => 'The proof file must be 5 MB or smaller.',
        ];
    }
}
