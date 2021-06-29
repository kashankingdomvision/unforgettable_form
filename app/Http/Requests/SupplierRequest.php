<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'username'          =>  'required|string',
            'email'         =>  'required|email',
            'categories'    =>  'required|array',
        ];
    }
    
    
    public function attributes()
    {
        return [
            'username'       => 'Name',
            'email'      => 'Email address',
            'categories' => 'Categories',
        ];
    }
}
