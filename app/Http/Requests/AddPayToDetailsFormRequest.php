<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPayToDetailsFormRequest extends FormRequest
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
           
            'pay_to_name' => 'required',
            'pay_to_code' => 'required',
        ];
    }

    public function messages(){
        return [
            'pay_to_name.required' => 'Pay to name field is required',
            'pay_to_code.required' => 'Pay to code field is required',
        ];
    }
}
