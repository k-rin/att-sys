<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OvertimeReportRequest extends FormRequest
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
            'date'     => 'required|date_format:Y-m-d',
            'start_at' => 'required|date_format:H:i',
            'end_at'   => 'required|date_format:H:i',
            'reason'   => 'required|string',
        ];
    }
}