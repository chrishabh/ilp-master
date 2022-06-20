<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleFormRequest extends FormRequest
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
            'id'            =>  'required',
           'role_request'   =>  'required|in:admin,client,delete'
        ];
    }

    public function messages(){
        return [
            'id.required' => 'User id field is required',
            'role_request.required' => 'Role field is required',
        ];
    }
}
