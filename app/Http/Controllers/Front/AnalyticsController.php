<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\Metric;
use Illuminate\Support\Facades\Log;
use Validator;
use Response;
use App\Http\Controllers\Controller;
use DB;
use Mail;

class AnalyticsController extends Controller
{
//    public function online()
//    {
//        return response()->json(
//            \Cache::remember('ga4_online_count', 30, function () {
//                $propertyId      = config('services.ga4.property_id');
//                $credentialsPath = config('services.ga4.credentials');
//
//                $client = new BetaAnalyticsDataClient([
//                    'credentials' => $credentialsPath,
//                ]);
//
//                $request = (new RunRealtimeReportRequest())
//                    ->setProperty('properties/' . $propertyId)
//                    ->setMetrics([ new Metric(['name' => 'activeUsers']) ])
//                    ->setReturnPropertyQuota(true); // tuỳ chọn: xem quota trong $response
//
//                $response = $client->runRealtimeReport($request);
//
//                // 1) Thử đọc từ rows (nếu có dimension thì thường có rows)
//                $rows = iterator_to_array($response->getRows());
//                if (!empty($rows)) {
//                    return ['online' =>
//                        (int)$rows[0]->getMetricValues()[0]->getValue()
//                    ];
//                }
//
//                // 2) Không có rows ⇒ lấy từ totals (trường hợp KHÔNG set dimension)
//                $totals = iterator_to_array($response->getTotals());
//                if (!empty($totals)) {
//                    return ['online' =>
//                        (int)$totals[0]->getMetricValues()[0]->getValue()
//                    ];
//                }
//
//                // 3) Mặc định
//                return ['online' => 0];
//            })
//        );
//    }

    public function online()
    {
        return response()->json(
            Cache::remember('ga4_online_count', 30, function () {
                try {
                    $propertyId      = config('services.ga4.property_id');
                    $credentialsPath = config('services.ga4.credentials');

                    $accessToken = $this->ga4GetAccessToken($credentialsPath);

                    // Gọi Realtime API: activeUsers
                    $url  = "https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runRealtimeReport";
                    $body = [
                        'metrics' => [
                            ['name' => 'activeUsers'],
                        ],
                        // KHÔNG cần set 'property' trong body vì đã có trong URL path
                    ];

                    $res = $this->ga4PostJson($url, $accessToken, $body);

                    // Ưu tiên lấy từ rows; nếu không có thì lấy totals (khi không set dimension)
                    $online = 0;
                    if (!empty($res['rows'][0]['metricValues'][0]['value'])) {
                        $online = (int)$res['rows'][0]['metricValues'][0]['value'];
                    } elseif (!empty($res['totals'][0]['metricValues'][0]['value'])) {
                        $online = (int)$res['totals'][0]['metricValues'][0]['value'];
                    }

                    return ['online' => $online];
                } catch (\Throwable $e) {
                    Log::warning('GA4 realtime fallback to 0: '.$e->getMessage());
                    return ['online' => 0];
                }
            })
        );
    }

    // ----- Helpers: lấy token bằng JWT + gọi API -----

    private function ga4GetAccessToken(string $saJsonPath): string
    {
        $sa = json_decode(@file_get_contents($saJsonPath), true);
        if (!$sa || empty($sa['private_key']) || empty($sa['client_email'])) {
            throw new \RuntimeException('Service account JSON không hợp lệ hoặc không đọc được.');
        }

        $aud = !empty($sa['token_uri']) ? $sa['token_uri'] : 'https://oauth2.googleapis.com/token';
        $now = time();

        $header  = ['alg' => 'RS256', 'typ' => 'JWT'];
        $payload = [
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud'   => $aud,
            'exp'   => $now + 3600,
            'iat'   => $now,
        ];

        $signingInput = $this->b64url(json_encode($header)).'.'.$this->b64url(json_encode($payload));
        $signature = '';
        if (!openssl_sign($signingInput, $signature, $sa['private_key'], 'sha256WithRSAEncryption')) {
            throw new \RuntimeException('Không ký được JWT.');
        }
        $jwt = $signingInput.'.'.$this->b64url($signature);

        // Exchange JWT -> access_token
        $ch = curl_init($aud);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $res = curl_exec($ch);
        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL token error: '.$err);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($res, true);
        if ($code !== 200 || empty($data['access_token'])) {
            throw new \RuntimeException('Token response lỗi: '.$res);
        }
        return $data['access_token'];
    }

    private function ga4PostJson(string $url, string $accessToken, array $body): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $res = curl_exec($ch);
        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL realtime error: '.$err);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) {
            throw new \RuntimeException('GA4 realtime API trả về lỗi: '.$res);
        }
        return json_decode($res, true) ?: [];
    }

    private function b64url(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }


}
