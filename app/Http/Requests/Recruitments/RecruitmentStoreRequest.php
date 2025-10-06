<?php

namespace App\Http\Requests\Recruitments;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RecruitmentStoreRequest extends BaseRequest
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
            'description' => 'required',
            'salary' => 'required',
            'expiration_date' => 'required',
            'address' => 'required',
        ];

        return $rules;
    }

}
