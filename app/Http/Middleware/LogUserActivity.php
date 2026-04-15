<?php

namespace App\Http\Middleware;

use App\Services\AuditTrailService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user()) {
            return $response;
        }

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        if (in_array($routeName, ['logout'], true)) {
            return $response;
        }

        $this->auditTrailService->log([
            'module' => $this->resolveModule($routeName, $request),
            'action' => $this->resolveAction($request),
            'description' => $this->auditTrailService->describeRequest($request),
            'reference_table' => null,
            'reference_id' => null,
        ], $request);

        return $response;
    }

    private function resolveModule(?string $routeName, Request $request): string
    {
        if ($routeName) {
            return str_replace('.', ':', $routeName);
        }

        return $request->segment(1) ?: 'system';
    }

    private function resolveAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'action',
        };
    }
}
