<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendingMoneyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference' => 'required|string|max:100',
            'date' => 'required|string|date',
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required|string|size:3',
            'sender_account' => 'required|string',
            'receiver_bank_code' => 'required|string|max:255',
            'receiver_account' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|min:2|max:255',
            'notes'  => 'nullable|array',
            'notes.*' => 'required_with:notes|string|min:1|max:2000',
            'payment_type' => 'nullable|integer|min:0',
            'charge_details' => 'nullable|string|max:255',
        ];
    }
}
