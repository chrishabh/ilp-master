<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkUserAndProjectsFormRequest extends FormRequest
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
           
            'user_id' => 'required',
            'operation' => 'required',
            'project_id' => 'required',
        ];
    }

    public function messages(){
        return [
            'user_id.required' => 'User Id field is required',
            'operation.required' => 'Operation field is required',
            'project_id.required' => 'Project Id field is required',
        ];
    }
}
