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
            'pay_to' => 'required',
            'trade' => 'required',
            'level' => 'required',
            'block_id' => 'required',
            'plot_or_room' => 'required',
            'description_work' => 'required',
            'sum' => 'required',
        ];
    }

    public function messages(){
        return [
            'pay_to.required' => 'Pay to field is required',
            'trade.required' => 'Trade field is required',
            'level.required' => 'Level field is required',
            'plot_or_room.required' => 'Plot/Room field is required',
            'description_work.required' => 'Descritption work is required',
            'sum.required' => 'Sum field is required',
        ];
    }
}
