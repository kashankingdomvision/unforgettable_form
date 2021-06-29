<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'username'  => 'required|string',
            'email'     => 'required|email',
            'role'      => 'required',
        ];
    }
    
    public function attributes()
    {
        return [
            'username'  => 'Username',
            'email'     => 'Email address',
            'role'      => 'Role name',
        ];
    }
}