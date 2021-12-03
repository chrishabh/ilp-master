<?php

namespace App\Http\Requests;

use App\Enums\Constants;
use Illuminate\Foundation\Http\FormRequest;

class BookWagesFormRequest extends FormRequest
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
            case Constants::BOOK_WAGES:
                return [
                    'book_wages'=> 'required|array',
                    'book_wages.*.pay_to' => 'required',
                    'book_wages.*.trade' => 'required',
                    'book_wages.*.wages' => 'required',
                    'book_wages.*.level' => 'required',
                    'book_wages.*.block_id' => 'required',
                    'book_wages.*.plot_or_room' => 'required',
                    'book_wages.*.description_work' => 'required',
                    'book_wages.*.sum' => 'required',
                    'book_wages.*.project_id' => 'required',
                    'book_wages.*.block_id' => 'required',
                    'book_wages.*.apartment_id' => 'required',
                    'book_wages.*.main_description_id' => 'required'
                ];
            break;

            case Constants::EDIT_BOOKED_WAGES:
                return [
                    'id' => 'required',
                    'pay_to' => 'required',
                    'trade' => 'required',
                    'level' => 'required',
                    'block_id' => 'required',
                    'plot_or_room' => 'required',
                    'description_work' => 'required',
                    'sum' => 'required',
                    'project_id' => 'required',
                    'block_id' => 'required',
                    'apartment_id' => 'required',
                    'main_description_id' => 'required'
                ];
            break;

            case Constants::DELETE_BOOKED_WAGES:
                return [
                    'id' => 'required'
                ];
            break;

            default:
            return [];
            break;

        }
       
    }

    public function messages(){

        switch($this->path()){
            case Constants::BOOK_WAGES:
                return [
                    'book_wages.*.pay_to.required' => 'Pay to field is required',
                    'book_wages.*.trade.required' => 'Trade field is required',
                    'book_wages.*.level.required' => 'Level field is required',
                    'book_wages.*.wages.required' => 'Wages field is required',
                    'book_wages.*.plot_or_room.required' => 'Plot/Room field is required',
                    'book_wages.*.description_work.required' => 'Descritption work is required',
                    'book_wages.*.sum.required' => 'Sum field is required',
                ];
            break;

            case Constants::EDIT_BOOKED_WAGES:
                return [
                    'pay_to.required' => 'Pay to field is required',
                    'trade.required' => 'Trade field is required',
                    'level.required' => 'Level field is required',
                    'plot_or_room.required' => 'Plot/Room field is required',
                    'description_work.required' => 'Descritption work is required',
                    'sum.required' => 'Sum field is required',
                ];
            break;

            default:
            return [];
            break;

        }
        
    }
}
