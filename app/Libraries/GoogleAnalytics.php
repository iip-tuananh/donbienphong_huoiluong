<?php 
namespace App\Libraries;

use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class GoogleAnalytics{

    static function country() {
        $country = Analytics::performQuery(Period::days(14),'ga:sessions',  ['dimensions'=>'ga:country','sort'=>'-ga:sessions']);
        $result= collect($country['rows'] ?? [])->map(function (array $dateRow) {
            return [
                'country' =>  $dateRow[0],
                'sessions' => (int) $dateRow[1],
            ];
        });
        /* $data['country'] = $result->pluck('country'); */
        /* $data['country_sessions'] = $result->pluck('sessions'); */
        return $result;
    }

    static function fetchDeviceVisitors(Period $period)
    {
        $devices = Analytics::performQuery($period,'ga:users',  ['dimensions'=>'ga:deviceCategory']);
        $result= collect($devices['rows'] ?? [])->map(function (array $dateRow) {
            return [
                'device' =>  $dateRow[0],
                'count' => (int) $dateRow[1],
            ];
        });
        return $result;
    }

    static function fetchOrganicSearch (Period $period)
    {
        $organic_search = Analytics::performQuery($period,'ga:pageviews',  ['dimensions'=>'ga:source','sort'=> '-ga:pageviews']);
        $result= collect($organic_search['rows'] ?? [])->map(function (array $dateRow) {
            return [
                'source' =>  $dateRow[0],
                'count' => (int) $dateRow[1],
            ];
        });
        
        return $result;
    }

    static function topbrowsers()
    {
        $analyticsData = Analytics::fetchTopBrowsers(Period::days(14));
        $array = $analyticsData->toArray();
        foreach ($array as $k=>$v)
        {
            $array[$k] ['label'] = $array[$k] ['browser'];
            unset($array[$k]['browser']); 
            $array[$k] ['value'] = $array[$k] ['sessions'];
            unset($array[$k]['sessions']); 
            $array[$k]['color'] = $array[$k]['highlight'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        return json_encode($array);
    }

}