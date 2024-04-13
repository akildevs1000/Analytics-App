<?php

namespace App\Http\Requests\BranchShiftManagers;

use App\Traits\failedValidationWithName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use failedValidationWithName;
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
            'employees_table_id' => 'required',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
        ];
    }
}
