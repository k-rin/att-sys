<?php

namespace App\Http\Requests;

use App\Enums\CompensationType;
use Illuminate\Foundation\Http\FormRequest;

class SubAttendanceReportRequest extends FormRequest
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
            'date'         => 'required|date_format:Y-m-d',
            'reason'       => 'required|string',
            'compensation' => 'required|enum_value:' . CompensationType::class,
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
            'compensation' => intval($this->compensation),
        ]);
    }
}