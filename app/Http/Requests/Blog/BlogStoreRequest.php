<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class BlogStoreRequest extends BaseRequest
{
    /**
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
        $rules = [
            'cate_id' => 'required',
            'title' => 'required',
            'title_eng' => 'required',
            'body' => 'required',
            'body_eng' => 'required',
            'status' => 'required',
            'intro_eng' => 'required',
            'intro' => 'required',
        ];

        return $rules;
    }

}
