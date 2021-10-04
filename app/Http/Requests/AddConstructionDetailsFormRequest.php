<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddConstructionDetailsFormRequest extends FormRequest
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
            'construction_details' => 'required|array',
            'construction_details.*.description' => 'required',
            'construction_details.*.area' => 'required',
            'construction_details.*.unit' => 'required',
            'construction_details.*.lab_rate' => 'required',
            'construction_details.*.total' => 'required',
            'construction_details.*.amount_booked' => 'required',
            'construction_details.*.wages' => 'required',
            'construction_details.*.quantity' => 'required',
            'construction_details.*.booking_description' => 'required',
            'construction_details.*.floor' => 'required'
        ];
    }

    public function messages(){
        return [
            'project_id.required' => 'Project field is required',
            'block_id.required' => 'Block field is required',
            'apartment_id.required' => 'Apartment field is required',
            'main_description_id.required' => 'Main Description id field is required',
            'sub_description_id.required' => 'Sub Descritption id is required',
            'construction_details.required' => 'Construction Details aaray is required',
        ];
    }
}
