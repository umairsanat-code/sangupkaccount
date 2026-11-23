<?php

namespace App\Http\Requests\Admin\Team;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'editedfirstName' => 'required',
            'editedlastName' => 'required',
            'editedrole' => 'required',
            'editedimage' => 'image|mimes:jpeg,jpg,png,gif,svg,tiff|dimensions:max_width=8000,max_height=8000,ratio=1/1',

        ];
    }
    public function messages()
    {
        return [
            'editedimage.required' => 'Team member image is required',
            'editedimage.mimes' => 'Only (peg,jpg,png,gif,svg,tiff) extensions and 10MB files are allowed',
            'editedimage.dimensions' => 'Images with same height and width are allowed e.g (524x524,1024x1024)',


        ];
    }
}
