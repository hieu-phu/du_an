<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'user_id' => $request->string('user_id')->toString(),
            'module' => $request->string('module')->toString(),
            'action' => $request->string('action')->toString(),
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'per_page' => $request->integer('per_page', 20),
        ];

        $activityLogs = ActivityLog::query()
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('activity_logs.description', 'like', '%' . $search . '%')
                        ->orWhere('activity_logs.ip_address', 'like', '%' . $search . '%')
                        ->orWhere('activity_logs.device', 'like', '%' . $search . '%')
                        ->orWhere('activity_logs.user_agent', 'like', '%' . $search . '%')
                        ->orWhere('users.name', 'like', '%' . $search . '%')
                        ->orWhere('users.email', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['user_id'], fn ($query, $userId) => $query->where('activity_logs.user_id', $userId))
            ->when($filters['module'], fn ($query, $module) => $query->where('activity_logs.module', $module))
            ->when($filters['action'], fn ($query, $action) => $query->where('activity_logs.action', $action))
            ->when($filters['date_from'], fn ($query, $dateFrom) => $query->whereDate('activity_logs.occurred_at', '>=', $dateFrom))
            ->when($filters['date_to'], fn ($query, $dateTo) => $query->whereDate('activity_logs.occurred_at', '<=', $dateTo))
            ->orderByDesc('activity_logs.occurred_at')
            ->orderByDesc('activity_logs.id')
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn ($log) => [
                'id' => $log->id,
                'user_id' => $log->user_id,
                'user_name' => $log->user_name,
                'user_email' => $log->user_email,
                'module' => $log->module,
                'action' => $log->action,
                'description' => $log->description,
                'reference_table' => $log->reference_table,
                'reference_id' => $log->reference_id,
                'ip_address' => $log->ip_address,
                'device' => $log->device,
                'user_agent' => $log->user_agent,
                'occurred_at' => optional($log->occurred_at)->format('Y-m-d H:i:s'),
            ]);

        $loginHistories = LoginHistory::query()
            ->with('user:id,name,email')
            ->when($filters['user_id'], fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('ip_address', 'like', '%' . $search . '%')
                        ->orWhere('device', 'like', '%' . $search . '%')
                        ->orWhere('browser', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($filters['date_from'], fn ($query, $dateFrom) => $query->whereDate('login_at', '>=', $dateFrom))
            ->when($filters['date_to'], fn ($query, $dateTo) => $query->whereDate('login_at', '<=', $dateTo))
            ->latest('login_at')
            ->limit(20)
            ->get()
            ->map(fn (LoginHistory $history) => [
                'id' => $history->id,
                'user_name' => $history->user?->name,
                'user_email' => $history->user?->email,
                'login_at' => optional($history->login_at)->format('Y-m-d H:i:s'),
                'logout_at' => optional($history->logout_at)->format('Y-m-d H:i:s'),
                'ip_address' => $history->ip_address,
                'device' => $history->device,
                'browser' => $history->browser,
            ])
            ->values();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'label' => "{$user->name} ({$user->email})",
            ])
            ->values();

        $modules = ActivityLog::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module')
            ->values();

        $actions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->values();

        return Inertia::render('AuditLogs/Index', [
            'filters' => $filters,
            'activityLogs' => $activityLogs,
            'loginHistories' => $loginHistories,
            'users' => $users,
            'modules' => $modules,
            'actions' => $actions,
        ]);
    }
}
