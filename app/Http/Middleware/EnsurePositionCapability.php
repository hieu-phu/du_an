<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware kiểm tra quyền hạn nghiệp vụ của chức vụ.
 *
 * Cách dùng trong routes:
 *   ->middleware('position.capability:approve_attendance')
 *   ->middleware('position.capability:approve_attendance,approve_leave')
 *
 * Admin luôn pass. HR/Employee phải có chức vụ với capability tương ứng.
 */
class EnsurePositionCapability
{
    public function handle(Request $request, Closure $next, string ...$capabilities): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Bạn cần đăng nhập để thực hiện thao tác này.');
        }

        foreach ($capabilities as $capability) {
            if (!$user->hasPositionCapability($capability)) {
                if ($request->expectsJson() || $request->header('X-Inertia')) {
                    abort(403, "Chức vụ của bạn không có quyền: {$capability}.");
                }

                return redirect()->back()->withErrors([
                    'capability' => 'Chức vụ của bạn không có quyền thực hiện thao tác này.',
                ]);
            }
        }

        return $next($request);
    }
}
