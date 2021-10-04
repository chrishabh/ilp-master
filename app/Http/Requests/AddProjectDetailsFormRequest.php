<?php

namespace App\Http\Requests;

use App\Enums\Constants;
use Illuminate\Foundation\Http\FormRequest;

class AddProjectDetailsFormRequest extends FormRequest
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
            case Constants::ADD_PROJECT_DETAILS_ENDPOINT:
                return [
                    'project_name' => 'required',
                ];
            break;

            case Constants::ADD_BLOCK_DETAILS_ENDPOINT:
                return [
                    'project_id' => 'required',
                    'block_name' => 'required',
                ];
            break;

            case Constants::ADD_APARTMENT_DETAILS_ENDPOINT:
                return [
                    'project_id' => 'required',
                    'block_id' => 'required',
                    'apartment_number' => 'required'
                ];
            break;

            default:
            return [];
            break;

        }
    }

    public function messages(){
        return [
            'project_name.required' => 'Project Name field is required',
        ];
    }
}
