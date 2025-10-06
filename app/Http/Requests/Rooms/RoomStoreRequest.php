<?php

namespace App\Http\Requests\Rooms;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RoomStoreRequest extends BaseRequest
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
            'name' => 'required|unique:rooms,name',
            'name_eng' => 'required|unique:rooms,name_eng',
            'area_eng' => 'required',
            'area' => 'required',
            'status' => 'required|in:1,2',
            'view' => 'required',
            'view_eng' => 'required',
            'bedrooms' => 'required',
            'bedrooms_eng' => 'required',
            'description' => 'required',
            'description_eng' => 'required',
//            'highlight' => 'required',
//            'amenities' => 'required',
            'maximum_occupancy' => 'required',
            'maximum_occupancy_eng' => 'required',
            'galleries' => 'required|array|min:1|max:20',
            'galleries.*.image' => 'required|file|mimes:png,jpg,jpeg|max:10000',
        ];

        return $rules;
    }

}
