<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends BaseRequest
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
            'title' => 'required',
            'des' => 'required',
            'short_des' => 'required',
        ];

        return $rules;
    }

}
