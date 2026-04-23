<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(
            (int) $request->user()->id,
            $request->boolean('unread_only'),
            max(1, min(100, (int) $request->integer('per_page', 20)))
        );

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => $this->notificationService->getUnreadCount((int) $request->user()->id),
            'by_category' => $this->notificationService->getUnreadCountByCategory((int) $request->user()->id),
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($this->canManageNotification($request, $notification), 403);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'updated' => $this->notificationService->markAllAsRead((int) $request->user()->id),
        ]);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($this->canManageNotification($request, $notification), 403);

        $notification->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    private function canManageNotification(Request $request, Notification $notification): bool
    {
        return $notification->user_id === null
            || (int) $notification->user_id === (int) $request->user()->id;
    }
}
