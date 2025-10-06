<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function responseSuccess($message = "", $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message ?: "Thao tác thành công",
            'data' => $data
        ]);
    }

    /**
     * @param string $message
     * @param null $errors
     * @param int $code
     * @return JsonResponse
     */
    protected function responseErrors($message = "", $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message ?: "Thao tác thất bại",
            'errors' => $errors
        ]);
    }
}
