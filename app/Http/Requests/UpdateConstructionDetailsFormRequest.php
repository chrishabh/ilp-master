<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConstructionDetailsFormRequest extends FormRequest
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
           
            'block_id' => 'required',
            'project_id' => 'required',
            'sub_description_id' => 'required',
            'apartment_id' => 'required',
            'main_description_id' => 'required',
            'description' => 'required',
            'area' => 'required',
            'unit' => 'required',
            'lab_rate' => 'required',
            'total' => 'required',
            'amount_booked' => 'required',
            'wages' => 'required',
            'quantity' => 'required',
            'booking_description' => 'required',
            'floor' => 'required'
        ];
    }

    public function messages(){
        return [
            'project_id.required' => 'Project field is required',
            'block_id.required' => 'Block field is required',
            'apartment_id.required' => 'Apartment field is required',
            'main_description_id.required' => 'Main Description id field is required',
            'sub_description_id.required' => 'Sub Descritption id is required',
            'description.required' => 'Description field is required',
        ];
    }
}
