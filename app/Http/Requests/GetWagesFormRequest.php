<?php

namespace App\Http\Requests;

use App\Enums\Constants;
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
        switch($this->path()){

            case Constants::WAGES_REPORT:
                return [
                    'no_of_records' => 'required',
                    'page_no' => 'required',
                    'project_id' => 'required',
                    'wages_number' => 'required',
                    'user_id' => 'required',
                    'date' => 'required'
                ];
            break;

            case Constants::DOWNLOAD_WAGES_REPORT:
                return [
                    'no_of_records' => 'required',
                    'page_no' => 'required',
                    'project_id' => 'required',
                    'wages_number' => 'required',
                    'user_id' => 'required',
                    'date' => 'required'
                ];
            break;

            default:
                return [
                    'no_of_records' => 'required',
                    'page_no' => 'required',
                    'project_id' => 'required',
                    'wages_number' => 'required',
                    'user_id' => 'required',
                    #'apartment_id' => 'required'
                ];
            break;
        }
       
    }

    public function messages(){
        return [
            'no_of_records.required' => 'Number of records is required',
            'page_no.required' => 'Page Number is required',
        ];
    }
}
