<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;
use Illuminate\Support\Str;
use File as FileSystem;

class Ebrochure extends BaseModel
{
    protected $table = 'ebrochure';


    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public static function searchByFilter($request)
    {
        $result = self::with([
            'image',
        ]);

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        $obj = self::with('image')->where('id', $id)->firstOrFail();

        $obj->file_ = null;
        $obj->file_url = $obj->file;
        if($obj->file) {
            $array = explode(',', $obj->file);
            $array = array_map('trim', $array);
            $array = array_filter($array);
            $obj->file_ = basename($array[0]);
        }

        return $obj;
    }

    public function syncDocuments($documents, $folder)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $attachments = [];

        if ($documents) {
            foreach ($documents as $document)  {
                $filename = $document->getClientOriginalName();
                $name = Str::slug(str_replace("/", "", $filename));
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $destinationFileName = $name . '-' . time() . '-' . randomString(4);
                $destinationFile = $destinationFileName . '.' . $extension;
                $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;

                if (!is_dir($destinationPath)) {
                    FileSystem::makeDirectory($destinationPath, 0777, true);
                }

                $document->move($destinationPath, $destinationFile);

                $path = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]);

                array_push($attachments, $path);
            }
            $this->file = join(', ', $attachments);
            $this->save();
        }
    }

    public function canDelete()
    {
        return true;
    }
}
