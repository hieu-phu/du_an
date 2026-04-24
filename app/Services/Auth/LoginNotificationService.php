<?php

namespace App\Services\Auth;

use App\Mail\AdminUserLoginAlertMail;
use App\Mail\SuccessfulLoginMail;
use App\Models\LoginHistory;
use App\Models\User;
use App\Services\AuditTrailService;
use App\Support\AccessMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginNotificationService
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {
    }

    public function handleSuccessfulLogin(User $user, Request $request, string $loginMethod): void
    {
        $ipAddress = $request->ip() ?? 'Không xác định';
        $userAgent = (string) ($request->userAgent() ?: 'Không xác định');
        $loggedInAt = now('Asia/Ho_Chi_Minh');
        $device = $this->auditTrailService->detectDevice($userAgent) ?? 'Unknown device';
        $browser = $this->auditTrailService->detectBrowser($userAgent) ?? 'Unknown browser';

        $user->forceFill([
            'last_login_at' => $loggedInAt,
            'last_login_ip' => $ipAddress,
        ])->save();

        LoginHistory::query()->create([
            'user_id' => $user->id,
            'login_at' => $loggedInAt,
            'ip_address' => $ipAddress,
            'device' => $device,
            'browser' => $browser,
        ]);

        $this->auditTrailService->log([
            'user_id' => $user->id,
            'module' => 'auth',
            'action' => 'login',
            'description' => "Dang nhap thanh cong bang {$loginMethod}",
            'reference_table' => 'users',
            'reference_id' => $user->id,
            'ip_address' => $ipAddress,
            'device' => $device,
            'user_agent' => $userAgent,
            'occurred_at' => $loggedInAt,
        ], $request);

        if (blank($user->email)) {
            return;
        }

        Mail::to($user->email)->queue(new SuccessfulLoginMail(
            user: $user->fresh(),
            loginMethod: $loginMethod,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            loggedInAt: $loggedInAt->format('d/m/Y H:i:s'),
        ));

        $this->notifyAdminsAboutUserLogin(
            $user->fresh(),
            $loginMethod,
            $ipAddress,
            $userAgent,
            $loggedInAt->format('d/m/Y H:i:s')
        );
    }

    private function notifyAdminsAboutUserLogin(
        User $user,
        string $loginMethod,
        string $ipAddress,
        string $userAgent,
        string $loggedInAt
    ): void {
        if (AccessMatrix::canApproveAnyRequest($user)) {
            return;
        }

        $admins = User::query()
            ->whereKeyNot($user->id)
            ->where('status', 'active')
            ->whereNotNull('email')
            ->with('employeeProfile.position')
            ->get(['id', 'name', 'email'])
            ->filter(fn (User $admin) => AccessMatrix::canApproveAnyRequest($admin));

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new AdminUserLoginAlertMail(
                admin: $admin,
                loggedInUser: $user,
                loginMethod: $loginMethod,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                loggedInAt: $loggedInAt,
            ));
        }
    }
}

