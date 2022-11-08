<?php

namespace App\Http\Requests;

use App\Enums\Constants;
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
        switch($this->path()){

            case Constants::DELETE_PAY_TO_DETAILS:
                return [
                    'id' => 'required'
                ];
            break;

            case Constants::UPLOAD_PAY_TO_DETAILS:
                return [];
            break;

            default:
                return [
                
                    'pay_to_name' => 'required',
                    'pay_to_code' => 'required',
                ];
            break;
        }
    }

    public function messages(){
        return [
            'pay_to_name.required' => 'Pay to name field is required',
            'pay_to_code.required' => 'Pay to code field is required',
            'id.required' => 'Pay to id field is required',
            'pay_to_code.required' => 'Pay to code field is required',
        ];
    }
}
