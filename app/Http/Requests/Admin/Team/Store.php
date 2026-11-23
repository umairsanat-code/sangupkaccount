<?php

namespace App\Http\Requests\Admin\Team;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class Store extends FormRequest
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
            'firstName'=>'required',
            'lastName'=>'required',
            'email'=>'required|email|unique:users',
            'pnr'=>'required',
            'total_member'=>'required|',

        ];
    }
    public function messages()
    {
        return [
            'image.required' => 'Team member image is required',
            'image.mimes' => 'Only (peg,jpg,png,gif,svg,tiff) extensions and 10MB files are allowed',
            'image.dimensions' => 'Images with same height and width are allowed e.g (524x524,1024x1024)',
            'password.required' => 'Team member password is required!',
            'password.min' => 'Password is required and must atleast 6 characters long!',


        ];
    }

       /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $error = collect($validator->errors())->collapse()->toArray();
        $errors = implode(' | ', $error);
        throw new HttpResponseException(response()->json(
            ['response' => ['status' => false, 'message' => $errors]],
            Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
