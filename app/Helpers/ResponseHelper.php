<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($code, $message = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $code);
    }

    public static function randomCode($digitNeeded): string
    {
        $random_number = '';
        $count = 0;
        while ($count < $digitNeeded) {
            $random_digit = mt_rand(1, 9);
            $random_number .= $random_digit;
            $count = strlen($random_number);
        }

        return $random_number;
    }
}
