<?php

namespace App\Http\Requests;

use App\Enums\AdminRole;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
        return $this->isMethod('post')
            ? $this->store()
            : $this->update();
    }

    protected function store()
    {
        return [
            'email'    => 'required|unique:admins|email',
            'password' => 'required|string',
            'role'     => 'required|string|enum_value:' . AdminRole::class,
            'locked'   => 'required|boolean',
        ];
    }

    protected function update()
    {
        return [
            'email'    => 'required|email',
            'password' => 'nullable|string',
            'role'     => 'required|string|enum_value:' . AdminRole::class,
            'locked'   => 'required|boolean',
        ];
    }
}