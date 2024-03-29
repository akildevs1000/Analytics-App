<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'full_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'profile_picture' => 'string',
            'system_user_id' => 'required|string|unique:customers',
            'type' => 'required|string',
            'date' => 'required|date',
            'company_id' => 'required|integer|min:0',
            'branch_id' => 'nullable',
        ];
    }
}
