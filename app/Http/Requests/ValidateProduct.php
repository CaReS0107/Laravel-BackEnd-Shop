<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateProduct extends FormRequest
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
            'name'=>'required|min:5',
            'price'=>'required',
            'description'=>'required|min:10',
            'category_id'=>'required|exists:categories,id'


        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Field Name is required',
            'name.min:5'=>'Name must have minimum 5 character',
            'price'=>'Field price is required',
            'description.required'=>'Field Description is required',
            'description.min:10'=>'Description must have minimum 10 characters',
            'category_id.required'=>'Category ID is required',
            'category_id.exists'=>'Input categories is not exist'

        ];
    }
}
