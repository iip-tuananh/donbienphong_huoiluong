<?php

namespace App\Http\Requests\Document;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class DocumentUpdateRequest extends BaseRequest
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
            'title' => 'required|unique:documents,title,'.$this->route('id').",id",
            'title_eng' => 'required|unique:documents,title_eng,'.$this->route('id').",id",
            'videos.*.link' => 'required',
        ];

        return $rules;
    }

}
