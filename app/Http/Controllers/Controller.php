<?php

namespace App\Http\Controllers;

use App\Helpers\InvoiceHelper;
use App\Helpers\MoneyHelper;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected int|null $currentUserId = null;
    protected \Illuminate\Support\Carbon $currentDateTime;

    public function __construct()
    {
        $currentUser = Auth::user();

        if ($currentUser) {
            $this->currentUserId = $currentUser->id;
        }

        $this->currentDateTime = now('Asia/Ho_Chi_Minh');
    }

    /**
     * Trả về JSON response chuẩn (Thành công).
     */
    protected function sendSuccess(mixed $data = null, string $message = 'Success', int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Trả về JSON response chuẩn (Lỗi).
     */
    protected function sendError(string $message = 'Error', mixed $errors = null, int $status = 400): \Illuminate\Http\JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Trả về JSON response mặc định (Legacy support).
     */
    protected function sendResponse(mixed $data, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * Parse date range từ string "date1,date2" hoặc array [date1, date2].
     *
     * @return array{0: string|null, 1: string|null}
     */
    protected function parseDateRange(string|array|null $dateRange): array
    {
        if (is_array($dateRange)) {
            return [$dateRange[0] ?? null, $dateRange[1] ?? null];
        }

        if (is_string($dateRange)) {
            $parts = explode(',', $dateRange);
            return [$parts[0] ?? null, $parts[1] ?? null];
        }

        return [null, null];
    }
}
