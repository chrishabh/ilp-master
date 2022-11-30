<?php

namespace App\Http\Requests;

use App\Enums\Constants;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordFormRequest extends FormRequest
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
            case Constants::FORGOT_PASSWORD:
                return [
                    'email' => 'required|email',
                    'password' => 'required'
                ];
            break;

            case Constants::DECRYPT_PASSWORD:
                return [
                    'email' => 'required|email'
                ];
            break;
            default:
            return [];
            break;

        }
    }

    public function messages(){
        return [
            'email.required' => 'Email is required',
        ];
    }
}
