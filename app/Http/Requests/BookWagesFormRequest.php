<?php

namespace App\Http\Requests;

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
        return [
            'book_wages'=> 'required|array',
            'book_wages.*.pay_to' => 'required',
            'book_wages.*.trade' => 'required',
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
    }

    public function messages(){
        return [
            'book_wages.*.pay_to.required' => 'Pay to field is required',
            'book_wages.*.trade.required' => 'Trade field is required',
            'book_wages.*.level.required' => 'Level field is required',
            'book_wages.*.plot_or_room.required' => 'Plot/Room field is required',
            'book_wages.*.description_work.required' => 'Descritption work is required',
            'book_wages.*.sum.required' => 'Sum field is required',
        ];
    }
}
