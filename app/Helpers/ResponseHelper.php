<?php


namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($message, $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public static function error($message, $status, $errors = null): JsonResponse
    {
        $errors = $errors ?? (new ResponseHelper)->generateErrors($message);
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], Response::HTTP_OK);
    }

    private function generateErrors($error)
    {
        return [
            "message" => [
                $error
            ]
        ];
    }
}
