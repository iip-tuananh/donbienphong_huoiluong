<?php

namespace App\Helpers;

use File as FileSystem;
use App\Model\Common\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Storage;
use Image;
use App\Services\CloudflareImageService;

class FileHelper
{
    /**
     * @param UploadedFile[] $files
     * @param $folder
     * @param null $id
     * @param null $class
     * @return array
     */
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }

    public static function uploadFileToCloudflare($file, $id = null, $class = null, $custom = null)
    {
        $instance = app(self::class);
        if($file){
            $response = $instance->cloudflareService->uploadImage($file);
            $file_data = [
                'name' => $file->getClientOriginalName(),
                'path' => $response['result']['variants'][0],
                'custom_field' => $custom,
            ];

            if ($id && $class) {
                self::saveFile($file_data, $id, $class);
            }

            return $file_data;
        }else{
            return [
                'name' => 'Lỗi',
                'path' => 'Không tải được file lên Cloudflare',
                'custom_field' => $custom,
            ];
        }
    }

    public static function deleteFileFromCloudflare($file, $id, $class, $custom = null)
    {
        $instance = app(self::class);
        if (!empty($file->path)) {
            $urlimg = $file->path;

            // Kiểm tra xem có phải đường dẫn Cloudflare Image hay không
            if (filter_var($urlimg, FILTER_VALIDATE_URL) &&
                preg_match('/^https:\/\/imagedelivery\.net\/[A-Za-z0-9_-]+\/[A-Za-z0-9-]+\/(public|private)$/', $urlimg)) {
                $instance->cloudflareService->deleteImage($urlimg);
            }
        }
        if (!is_array($file->id)) {
            $fileIds = [$file->id];
        }
        File::query()
            ->where('model_id', $id)
            ->where('model_type', $class)
            ->where('custom_field', $custom)
            ->whereIn('id', $fileIds)
            ->delete();
        // todo: xóa file khỏi hệ thống
    }

    public static function uploadFiles($files, $folder, $id = null, $class = null, $custom = null)
    {
        $rsl = [];
        foreach ($files as $file) {
            $rsl[] = self::uploadFile($file, $folder, $id, $class, $custom);
        }
        return $rsl;
    }

    /**
     * @param UploadedFile $file
     * @param $folder
     * @param null $id
     * @param null $class
     * @return array
     */
    public static function uploadFile($file, $folder, $id = null, $class = null, $custom = null, $type = null)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;
        if ($file->isValid()) {
            // make destination file name
            $filename = $file->getClientOriginalName();
            $name = Str::slug($filename);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $destinationFileName = $name . '-' . time() . '-' . randomString(4);
            $destinationFile = $destinationFileName . '.' . $extension;
            if (!is_dir($destinationPath)) {
                FileSystem::makeDirectory($destinationPath, 0777, true);
            }
            // Resize ảnh nếu là ảnh bài viết, sản phẩm, dịch vụ
            // Type = 1 =>> sản phẩm
            // Type = 2 =>> Danh mục sản phẩm
            // Type = 3 =>> Bài viết
            // Type = 4 =>> Danh mục bài viết
            // Type = 5 =>> Logo thương hiệu sản phẩm
            // Type = 6 =>> banners
            // Type = 7 =>> favicon

            if ($type == 1) {
//                Image::make($file)->resize(600, 600, function ($constraint) {
//                    $constraint->aspectRatio();
//                }
//                )->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
                Image::make($file)->resize(600, 600,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->resizeCanvas(600, 600)
                ->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);

            } else if ($type == 2) {
                Image::make($file)->resize(120, 120)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 3) {
                Image::make($file)->resize(473, 358)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 5) {
                Image::make($file)->resize(115, 75)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 6) {
                Image::make($file)->resize(1920, 700)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            } else if ($type == 7) {
                Image::make($file)->resize(32, 32)->save($destinationPath . DIRECTORY_SEPARATOR . $destinationFile);
            }  else {
                $file->move($destinationPath, $destinationFile);
            }

            $file_data = [
                'name' => $filename,
                'path' => DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]),
                'custom_field' => $custom,
            ];

            if ($id && $class) {
                self::saveFile($file_data, $id, $class);
            }

            return $file_data;
        }
        return [];
    }

    public static function copyFile($fileObject, $folder, $id = null, $class = null, $custom = null)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;

        // make destination file name
        $info = pathinfo($fileObject->path);
        $name = $info['filename'];
        $extension = $info['extension'];
        $destinationFileName = $name . '-' . time() . '-' . randomString(4);
        $destinationFile = $destinationFileName . '.' . $extension;

        $originalPath = public_path($fileObject->path);
        $targetPath = implode(DIRECTORY_SEPARATOR, [$destinationPath, $destinationFile]);

        if (!is_dir($destinationPath)) {
            FileSystem::makeDirectory($destinationPath);
        }

        FileSystem::copy($originalPath, $targetPath);

        $file_data = [
            'name' => $fileObject->name,
            'path' => DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]),
            'custom_field' => $custom,
        ];

        if ($id && $class) {
            self::saveFile($file_data, $id, $class);
        }

        return $file_data;
    }

    public static function saveFile($file_data, $id, $class)
    {
        $file_data['model_id'] = $id;
        $file_data['model_type'] = $class;
        $file = new File($file_data);
        $file->save();

        return $file;
    }

    public static function updateFile($file_data, $id, $class)
    {
        $file_data['model_id'] = $id;
        $file_data['model_type'] = $class;
        $file = File::where('model_id', $id)->update($file_data);

        return $file;
    }

    /**
     * Chỉ cập nhật lại trong db, ko xóa khỏi db, ko xóa file
     * @param $fileIds
     * @param $id
     * @param $class
     */
    public static function deleteFiles($fileIds, $id, $class, $custom = null)
    {
        if (!is_array($fileIds)) {
            $fileIds = [$fileIds];
        }
        File::query()
            ->where('model_id', $id)
            ->where('model_type', $class)
            ->where('custom_field', $custom)
            ->whereIn('id', $fileIds)
            ->update([
                'model_id' => null,
                'model_type' => null
            ]);
    }

    /**
     * Xóa trong db và xóa file
     * @param $fileIds
     * @param $id
     * @param $class
     */
    public static function forceDeleteFiles($fileIds, $id, $class, $custom = null)
    {
        if (!is_array($fileIds)) {
            $fileIds = [$fileIds];
        }
        File::query()
            ->where('model_id', $id)
            ->where('model_type', $class)
            ->where('custom_field', $custom)
            ->whereIn('id', $fileIds)
            ->delete();
        // todo: xóa file khỏi hệ thống
    }
}
