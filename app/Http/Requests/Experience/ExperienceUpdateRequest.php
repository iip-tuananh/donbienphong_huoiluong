<?php

namespace App\Http\Requests\Experience;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ExperienceUpdateRequest extends BaseRequest
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
            'name' => 'required|unique:experience,name,'.$this->route('id').",id",
            'name_eng' => 'required|unique:experience,name_eng,'.$this->route('id').",id",
            'galleries' => 'nullable|array|min:1|max:20',
            'galleries.*.image' => 'nullable|file|mimes:png,jpg,jpeg|max:10000',
        ];

        return $rules;
    }

}
