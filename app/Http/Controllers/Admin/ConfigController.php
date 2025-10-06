<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Config;
use App\Model\Common\File;
use Illuminate\Http\Request;
use App\Model\Admin\Config as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use App\Http\Controllers\Controller;
use App\Helpers\FileHelper;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use DB;

class ConfigController extends Controller
{
	protected $view = 'admin.configs';
	protected $route = 'Config';

	public function edit()
	{
		$id = 1;
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact('object'));
	}

	public function update(Request $request)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'web_title' => 'required|max:255',
				'hotline' => 'required',
				'zalo' => 'required',
				'email' => 'required|email',
				'facebook' => 'nullable|max:255',
				'image' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:3000',
                'favicon' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:3000',
                'location' => 'nullable',
                'google_map' => 'nullable'
			]
		);

		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}

		DB::beginTransaction();
		try {
			$object = ThisModel::where('id',1)->first();
			$object->web_title = $request->web_title;
			$object->short_name_company = $request->short_name_company;
			$object->hotline = $request->hotline;
			$object->web_des = $request->web_des;
			$object->web_des_2 = $request->web_des_2;
			$object->web_des_3 = $request->web_des_3;
			$object->web_des_eng = $request->web_des_eng;
			$object->zalo = $request->zalo;
			$object->address_company = $request->address_company;
			$object->address_company_eng = $request->address_company_eng;
			$object->address_center_insurance = $request->address_center_insurance;
			$object->email = $request->email;
			$object->facebook = $request->facebook;
			$object->twitter = $request->twitter;
			$object->instagram = $request->instagram;
			$object->youtube = $request->youtube;
			$object->location = $request->location;
			$object->google_map = $request->google_map;
			$object->introduction = $request->introduction;
			$object->introduction_eng = $request->introduction_eng;
			$object->address = $request->address;
			$object->tax_code = $request->tax_code;

			$object->click_call = $request->click_call;
			$object->facebook_chat = $request->facebook_chat;
			$object->zalo_chat = $request->zalo_chat;
			$object->phone_switchboard = $request->phone_switchboard;
			$object->youtube_iframe = $request->youtube_iframe;
			$object->hdmh = $request->hdmh;
			$object->facebook_mess = $request->facebook_mess;
			$object->nganhang = $request->nganhang;
			$object->chutaikhoan = $request->chutaikhoan;
			$object->sotaikhoan = $request->sotaikhoan;
			$object->chinhanh = $request->chinhanh;
			$object->nguoidaidien = $request->nguoidaidien;
			$object->gpxb = $request->gpxb;

			$object->save();

			if($request->image) {
				if($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
				}
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
			}

            if($request->favicon) {
                if($object->favicon) {
                    FileHelper::deleteFileFromCloudflare($object->favicon, $object->id, ThisModel::class, 'favicon');
                }
                FileHelper::uploadFileToCloudflare($request->favicon, $object->id, ThisModel::class, 'favicon');
            }

            if($request->qr) {
                if($object->qr) {
                    FileHelper::deleteFileFromCloudflare($object->qr, $object->id, ThisModel::class, 'qr');
                }
                FileHelper::uploadFileToCloudflare($request->qr, $object->id, ThisModel::class, 'qr');
            }
//            if($request->video) {
//                $oldFile = File::query()->where('model_id', $object->id)
//                    ->where('model_type', Config::class)
//                    ->where('custom_field', 'video')
//                    ->first();
//
//                if ($oldFile) {
//                    $oldFilePath = public_path($oldFile->path);
//                    if (file_exists($oldFilePath)) {
//                        unlink($oldFilePath);
//                    }
//                    $oldFile->delete();
//                }
//
//                $uploadedFile = $request->file('video');
//                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
//                $destinationPath = public_path('uploads/media');
//                $uploadedFile->move($destinationPath, $filename);
//                $storedPath = 'uploads/media/' . $filename;
//
//                $newFile = new File();
//                $newFile->model_id = $object->id;
//                $newFile->model_type = Config::class;
//                $newFile->name = $filename;
//                $newFile->path = $storedPath;
//                $newFile->custom_field  = 'video';
//                $newFile->save();
//            }


            $object->syncGalleries($request->galleries);

            DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			return Response::json($json);
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}
}
