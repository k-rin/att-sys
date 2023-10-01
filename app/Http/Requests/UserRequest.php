<?php

namespace App\Http\Requests;

use App\Enums\UserSex;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email'         => 'required|email',
            'name'          => 'string',
            'alias'         => 'string',
            'sex'           => 'required|enum_value:' . UserSex::class,
            'birthday'      => 'date',
            'hire_date'     => 'date',
            'paid_leaves'   => 'numeric',
            'department_id' => 'exists:departments,id',
            'locked'        => 'boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'sex' => intval($this->sex),
        ]);
    }
}