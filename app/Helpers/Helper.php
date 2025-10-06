<?php

use \Carbon\Carbon;
use \App\Model\Uptek\G7Info;

if (!function_exists('getPrintHeader')) {
    function printBlock($id) {
        $block = \App\Model\Admin\Block::where('id', $id)->first();
        if(isset($block)) {
            return $block->body;
        }
        return '';

    }
}

if (!function_exists('randomString')) {
    function randomString($length = 8) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}

if (!function_exists('array_find_index')) {
    function array_find_index($arr, $func) {
        for ($i = 0; $i < count($arr); $i++) {
            if ($func($arr[$i])) return $i;
        }
        return -1;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }
}

if (!function_exists('array_find_el')) {
    function array_find_el($arr, $func) {
        $index = array_find_index($arr, $func);
        if ($index != -1) return $arr[$index];
        return null;
    }
}

if (!function_exists('withUnits')) {
    function withUnits() {
        return function($q) {
            $q->leftJoin('units', 'product_units.unit_id', '=', 'units.id')
                ->orderBy('product_units.is_base', 'DESC')
                ->orderBy('product_units.unit_coefficient', 'ASC')
                ->select(['product_units.*', 'units.name', 'units.english_name']);
        };
    }
}

if (!function_exists('getStatus')) {
    function getStatus($status, $statuses) {
        $obj = array_find_el($statuses, function($el) use ($status) {
            return $el['id'] == $status;
        });
        if (!$obj) return '';
        return '<span class="badge badge-'.$obj['type'].'">'.$obj['name'].'</span>';
    }
}
if (!function_exists('generateCode')) {
    function generateCode($length, $value) {
        $value = (string) $value;
        $cur_length = strlen($value);
        if ($length <= $cur_length) return $value;
        return str_repeat("0", $length - $cur_length).$value;
    }
}
if (!function_exists('formatCurrent')) {
    function formatCurrent($value) {
        return number_format($value, 0, '', ',');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($number, $precision = 0) {
        if (!$number) return '0';
        if ($precision != 0) {
            $result = number_format($number, $precision, '.', ',');
        } else {
            $result = number_format($number, 4, '.', ',');
            $result = preg_replace("/\.0+$/", "", $result);
            $result = preg_replace("/(\.\d*?[1-9]+)0+$/", "$1", $result);
        }
        return preg_replace("/\.$/", "", $result);
    }
}
if (!function_exists('setDefault')) {
    function setDefault($obj, $field, $default = null) {
        return $obj[$field] ?? $default;
    }
}

if (!function_exists('messageFromNotification')) {
    function messageFromNotification($notification) {
        $message = [
            'id' => $notification->id,
            'url' => $notification->url,
            'content' => $notification->content,
            'sender_name' => $notification->sender ? $notification->sender->name : "Admin",
            'sender_avatar' => ($notification->sender && $notification->sender->image) ? $notification->sender->image->path : asset('img/avatar.png'),
            'status' => 0,
            'created_at' => $notification->created_at
        ];
        return json_encode($message);
    }
}

if (!function_exists('getId')) {
    function getId($obj) {
        return isset($obj['id']) ? $obj['id'] : null;
    }
}

if (!function_exists('addDay')) {
    function addDay($date) {
        $day = new \Carbon\Carbon($date);
        $day->addDay();
        return $day;
    }
}

if (!function_exists('addDays')) {
    function addDays($date, $day) {
        $result = new \Carbon\Carbon($date);
        $result->addDays($day);
        return $result;
    }
}

function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'năm'),
        array(60 * 60 * 24 * 30 , 'tháng'),
        array(60 * 60 * 24 * 7, 'tuần'),
        array(60 * 60 * 24 , 'ngày'),
        array(60 * 60 , 'giờ'),
        array(60 , 'phút'),
        array(1 , 'giây')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name} trước";
    return $print;
}

function successResponse($message = "", $data = [], $other_data = []) {
    return response()->json(array_merge([
        'success' => true,
        'message' => $message ?: "Thao tác thành công",
        'data' => $data
    ], $other_data));
}

function errorResponse($message = "", $errors = null) {
    return response()->json([
        'success' => false,
        'message' => $message ?: "Thao tác thất bại",
        'errors' => $errors
    ]);
}

if (! function_exists('pct_change')) {
    /**
     * Generate percentage change between two numbers.
     *
     * @param int|float $old
     * @param int|float $new
     * @param int $precision
     * @return float
     */
    function pct_change($old, $new, int $precision = 2): float
    {
        if ($old == $new) {
            return  0;
        } elseif ($old == 0) {
            return 100;
        }

        $change = (($new - $old) / $old) * 100;

        return round($change, $precision);
    }
}

function fillReport($template, $data) {
	foreach ($data as $key => $value) {
		$template = preg_replace("/\{\{".$key."\}\}/", $value, $template);
	}
	return $template;
}

function clearNull($template) {
	$template = preg_replace("/\{\{.*?\}\}/", "", $template);
	return $template;
}

if (!function_exists('checkDiff')) {
    function checkDiff($val1, $val2) {
        if ($val1 == $val2) return false;
        if ($val1 == 0 && $val2 != 0) return true;
        if ($val1 != 0 && $val2 == 0) return true;
        if (!$val1 && !$val2) return false;
        return true;
    }
}

if (!function_exists('getVersions')) {
    function getVersions($id, $class) {
        return \App\Model\Common\Version::where('model_id', $id)->where('model_type', $class)
            ->select(['id', 'created_at as time', 'model_id', 'created_by'])
            ->with([
                'histories',
                'user' => function($q) {
                    $q->select(['id', 'name', 'avatar']);
                }
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }
}

if (!function_exists('getHost')) {
    function getHost($url) {
        $parse = parse_url($url);
        return $parse['host'];
    }
}

if (!function_exists('cleanURL')) {
    function cleanURL($string) {
        $string = strtolower(preg_replace( array('/[^a-z0-9\- ]/i', '/[ \-]+/'), array('', '-'), $string));
        return $string;
    }
}

if (!function_exists('getBanner')) {
    function getBanner($category) {
        return $category->banner ? url(str_replace("\\", "/", $category->banner->path)) : '';
    }
}
