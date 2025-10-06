<?php

namespace App\Http\Requests\Document;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class DocumentStoreRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /** request.
     *
     *  Get the validation rules that apply to the
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|unique:documents,title',
            'title_eng' => 'required|unique:documents,title_eng',
            'videos.*.link' => 'required',
        ];

        return $rules;
    }

}
