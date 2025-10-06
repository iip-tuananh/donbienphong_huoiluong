<?php

namespace App\Model\Admin;
use App\Model\BaseModel;
use App\Model\Common\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use File as FileSystem;

class ApplyRecruitment extends Model
{
    protected $table = 'apply_recruitments';

    public static function searchByFilter($request) {
        $result = self::query();

        if (!empty($request->fullname)) {
            $result = $result->where('fullname', 'like', '%' . $request->fullname . '%');
        }

        if (!empty($request->email)) {
            $result = $result->where('email', 'like', '%' . $request->email . '%');
        }

        if (!empty($request->recruitment_id)) {
            $result = $result->where('recruitment_id', $request->recruitment_id);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function syncDocuments($documents, $folder)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $attachments = [$this->attachments];

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
            $this->attachments = join(', ', $attachments);
            $this->save();
        }
    }

}
