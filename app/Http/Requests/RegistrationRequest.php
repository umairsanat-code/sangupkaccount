<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RegistrationRequest extends FormRequest
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
            'email' => 'required|max:50|email|unique:users',
            'phoneNumber' => 'required|max:30|unique:users',
            'userName' => 'required|max:40',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:10240|dimensions:max_width=8000,max_height=8000',
            'deviceName'=>'required',
            'deviceIdentifier'=>'required',
            'fcmToken'=>'required',
            'socialSite'=>'nullable|in:APPLE',
            "password" => "required_unless:socialSite,APPLE",
        ];
    }

    /**
     * Get the error messages that apply to the request parameters.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'email is required!',
            'email.max' => 'Email Maximum 50 Character',
            'email.email' => 'Invalid email address',
            'email.unique' => 'Email already registered',
            'phoneNumber.required' => 'Phone Number is required',
            'phoneNumber.max' => 'Phone Number Maximum 30 Character',
            'phoneNumber.unique' => 'Phone Number already registered',
            'firstName.required' => 'First name is required',
            'firstName.max' => 'First name Maximum 30 Character',
            'avatar.required' => 'Image is required',
            'lastName.required' => 'last name is required',
            'lastName.max' => 'last name Maximum 30 Character',
            'password.required' => 'password is required',
            'password.max' => 'password Maximum 255 Character',
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
