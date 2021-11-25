<?php

namespace App\Http\Requests;

use App\Enums\Constants;
use Illuminate\Foundation\Http\FormRequest;

class EditConstructionDetailsFormRequest extends FormRequest
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
            'project_id' => 'required',
            'block_id' => 'required',
            'apartment_id' => 'required',
            'main_description_id' => 'required',
            'sub_description_id' => 'required',
            'id' => 'required',
            'description' =>  'required',
            'area' => 'required',
            'lab_rate' => 'required',
            'unit' =>  'required',
        ];
      
      
    }

    public function messages(){
        return [
            'no_of_records.required' => 'Number of records is required',
            'page_no.required' => 'Page Number is required',
            'project_id.required' => 'Project Id is required',
            'block_id.required' => 'Block Id is required',
            'apartment_id.required' => 'Apartment Id is required',
        ];
    }
}
