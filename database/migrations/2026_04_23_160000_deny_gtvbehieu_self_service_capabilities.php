<?php

use App\Support\PositionCapability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const USER_EMAIL = 'gtvbehieu@gmail.com';
    private const REASON = 'Disable self-service attendance, leave, and personal salary for system operator account.';

    public function up(): void
    {
        if (
            !Schema::hasTable('users')
            || !Schema::hasTable('position_capabilities')
            || !Schema::hasTable('user_position_capability_overrides')
        ) {
            return;
        }

        $userId = DB::table('users')
            ->where('email', self::USER_EMAIL)
            ->value('id');

        if (!$userId) {
            return;
        }

        $capabilityIds = DB::table('position_capabilities')
            ->whereIn('code', $this->blockedCapabilities())
            ->pluck('id');

        $now = now();

        foreach ($capabilityIds as $capabilityId) {
            DB::table('user_position_capability_overrides')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'capability_id' => $capabilityId,
                ],
                [
                    'effect' => 'deny',
                    'reason' => self::REASON,
                    'expires_at' => null,
                    'created_by' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        if (
            !Schema::hasTable('users')
            || !Schema::hasTable('position_capabilities')
            || !Schema::hasTable('user_position_capability_overrides')
        ) {
            return;
        }

        $userId = DB::table('users')
            ->where('email', self::USER_EMAIL)
            ->value('id');

        if (!$userId) {
            return;
        }

        $capabilityIds = DB::table('position_capabilities')
            ->whereIn('code', $this->blockedCapabilities())
            ->pluck('id');

        DB::table('user_position_capability_overrides')
            ->where('user_id', $userId)
            ->whereIn('capability_id', $capabilityIds)
            ->where('reason', self::REASON)
            ->delete();
    }

    private function blockedCapabilities(): array
    {
        return [
            PositionCapability::CHECK_IN,
            PositionCapability::CHECK_OUT,
            PositionCapability::VIEW_OWN_ATTENDANCE,
            PositionCapability::VIEW_OWN_SALARY,
            PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT,
        ];
    }
};
