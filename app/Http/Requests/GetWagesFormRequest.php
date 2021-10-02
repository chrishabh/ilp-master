<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWagesFormRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'no_of_records' => 'required',
            'page_no' => 'required',
        ];
    }

    public function messages(){
        return [
            'no_of_records.required' => 'Number of records is required',
            'page_no.required' => 'Page Number is required',
        ];
    }
}
