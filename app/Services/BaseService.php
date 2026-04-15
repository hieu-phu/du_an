<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class BaseService
{
    /**
     * Trả về user hiện tại (lazy — an toàn với queue/artisan commands).
     */
    protected function user(): ?Authenticatable
    {
        return auth()->user();
    }

    /**
     * Helper methods cho các thao tác chung (ví dụ: handle Transaction)
     */
    protected function handleTransaction(\Closure $callback)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $result = $callback();
            \Illuminate\Support\Facades\DB::commit();
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Transaction Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    protected function audit(
        string $module,
        string $action,
        ?string $description = null,
        ?string $referenceTable = null,
        $referenceId = null,
        ?Request $request = null
    ): void {
        app(AuditTrailService::class)->log([
            'user_id' => $this->user()?->getAuthIdentifier(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'reference_table' => $referenceTable,
            'reference_id' => $referenceId,
        ], $request);
    }
}
