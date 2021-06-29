<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeasonRequest extends FormRequest
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
            'name'          => 'required|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date',
            'default'       => 'required',
        ];
    }
    
    public function attributes()
    {
        return [
            'name'          => 'Season name',
            'start_date'    => 'Season start date',
            'end_date'      => 'Season end date',
            'default'       => 'Select default season',
        ];
    }
}
