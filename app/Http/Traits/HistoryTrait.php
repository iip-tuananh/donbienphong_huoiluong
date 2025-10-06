<?php

namespace App\Http\Traits;
use App\Model\Common\Version;
use Auth;

trait HistoryTrait
{
    public function createVersion() {
        $self = get_called_class();
        $version_data = self::getDataForVersion($this->id);
        $version = new Version();
        $version->model_id = $this->id;
        $version->model_type = self::class;
        $version->data = json_encode($version_data);
        $version->created_by = Auth::user()->id;
        $version->save();
        return $version;
    }

    public function saveHistory($version) {
        $origin = $this->getOriginal();
        $changes = $this->getAttributes();

        foreach($origin as $key => $value) {
            if (!in_array($key, $this->ignoreProperties, true) && checkDiff($origin[$key], $changes[$key])) {
                $old_value = $origin[$key];
                $new_value = $changes[$key];

                $this->createHistoryRecord($version, $key, $old_value, $new_value);
            }
        }
    }
}
