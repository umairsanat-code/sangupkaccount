<?php

namespace App\Http\Requests\Admin\Categories;

use Yoeunes\Toastr\Facades\Toastr;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
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
            'name'=>'required|unique:categories,name,NULL,id,deletedAt,NULL',
            'image'=>'required|image|mimes:jpeg,jpg,png,gif,svg,tiff'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Category image is required',
            'image.mimes' => 'Only (peg,jpg,png,gif,svg,tiff) extensions are allowed',
            'image.dimensions' => 'Images with same height and width are allowed e.g (524x524,1024x1024)',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $error = collect($validator->errors())->collapse()->toArray();
        $errors = implode(' | ', $error);
        throw new HttpResponseException(response()->json(
            ['response' => ['status' => false, 'message' => $errors]],
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }


}
