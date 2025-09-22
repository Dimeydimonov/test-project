<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseAdminController extends Controller
{
    
    protected function success($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    
    protected function error(string $message = '', int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    
    protected function deleted(string $message = 'Удаление прошло успешно'): JsonResponse
    {
        return $this->success(null, $message, 204);
    }
}
