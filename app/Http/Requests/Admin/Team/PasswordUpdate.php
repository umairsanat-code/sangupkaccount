<?php

namespace App\Http\Requests\Admin\Team;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdate extends FormRequest
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
         'password'=>'required|min:6|confirmed'

        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Team member password is required!',
            'password.min' => 'Password is required and must atleast 6 characters long!',



        ];
    }
}
