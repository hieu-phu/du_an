<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditTrailService
{
    public function log(array $payload, ?Request $request = null): ActivityLog
    {
        $request ??= request();
        $userAgent = (string) ($payload['user_agent'] ?? $request?->userAgent() ?? '');
        $occurredAt = $payload['occurred_at'] ?? now('Asia/Ho_Chi_Minh');

        return ActivityLog::query()->create([
            'user_id' => $payload['user_id'] ?? auth()->id(),
            'module' => $payload['module'] ?? 'system',
            'action' => $payload['action'],
            'description' => $payload['description'] ?? null,
            'reference_table' => $payload['reference_table'] ?? null,
            'reference_id' => $payload['reference_id'] ?? null,
            'ip_address' => $payload['ip_address'] ?? $request?->ip(),
            'device' => $payload['device'] ?? $this->detectDevice($userAgent),
            'user_agent' => $userAgent ?: null,
            'occurred_at' => $occurredAt,
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ]);
    }

    public function describeRequest(Request $request): string
    {
        $method = Str::upper($request->method());
        $routeName = $request->route()?->getName() ?? 'unknown';
        $path = $request->path();

        return "{$method} {$path} ({$routeName})";
    }

    public function detectDevice(?string $userAgent): ?string
    {
        if (blank($userAgent)) {
            return null;
        }

        $agent = Str::lower($userAgent);

        if (Str::contains($agent, ['iphone', 'ipad', 'ios'])) {
            return 'iPhone/iPad';
        }

        if (Str::contains($agent, ['android'])) {
            return 'Android';
        }

        if (Str::contains($agent, ['windows'])) {
            return 'Windows';
        }

        if (Str::contains($agent, ['macintosh', 'mac os'])) {
            return 'macOS';
        }

        if (Str::contains($agent, ['linux'])) {
            return 'Linux';
        }

        return 'Unknown device';
    }

    public function detectBrowser(?string $userAgent): ?string
    {
        if (blank($userAgent)) {
            return null;
        }

        $agent = Str::lower($userAgent);

        return match (true) {
            Str::contains($agent, 'edg') => 'Edge',
            Str::contains($agent, 'chrome') => 'Chrome',
            Str::contains($agent, 'firefox') => 'Firefox',
            Str::contains($agent, 'safari') && !Str::contains($agent, 'chrome') => 'Safari',
            Str::contains($agent, 'opera') || Str::contains($agent, 'opr/') => 'Opera',
            default => 'Unknown browser',
        };
    }
}
