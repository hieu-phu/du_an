<?php

use App\Support\PositionCapability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('position_capabilities')) {
            return;
        }

        $now = now();
        $newCapabilities = [
            PositionCapability::APPROVE_USER_REQUESTS => [
                'name' => 'Duyet yeu cau nhan su',
                'module' => 'approval',
                'description' => 'Phe duyet tao moi va thay doi thong tin nhan su',
            ],
            PositionCapability::APPROVE_DEPARTMENT_REQUESTS => [
                'name' => 'Duyet yeu cau phong ban',
                'module' => 'approval',
                'description' => 'Phe duyet tao moi, cap nhat va khoa/mo phong ban',
            ],
            PositionCapability::APPROVE_SALARY_REQUESTS => [
                'name' => 'Duyet yeu cau luong',
                'module' => 'approval',
                'description' => 'Phe duyet thay doi luong co ban',
            ],
        ];

        foreach ($newCapabilities as $code => $meta) {
            DB::table('position_capabilities')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $meta['name'],
                    'module' => $meta['module'],
                    'description' => $meta['description'],
                    'is_system' => true,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $oldCapabilityId = DB::table('position_capabilities')
            ->where('code', PositionCapability::APPROVE_REQUESTS)
            ->value('id');

        $newCapabilityIds = DB::table('position_capabilities')
            ->whereIn('code', array_keys($newCapabilities))
            ->pluck('id', 'code');

        if ($oldCapabilityId) {
            $positionsWithLegacyApproval = collect();

            if (Schema::hasTable('position_capability_position')) {
                $positionsWithLegacyApproval = DB::table('position_capability_position')
                    ->where('capability_id', $oldCapabilityId)
                    ->pluck('position_id');

                foreach ($newCapabilityIds as $capabilityId) {
                    $pivotRows = $positionsWithLegacyApproval->map(fn ($positionId) => [
                        'position_id' => $positionId,
                        'capability_id' => $capabilityId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    if (!empty($pivotRows)) {
                        DB::table('position_capability_position')->insertOrIgnore($pivotRows);
                    }
                }

                DB::table('position_capability_position')
                    ->where('capability_id', $oldCapabilityId)
                    ->delete();
            }

            if (Schema::hasTable('positions') && Schema::hasColumn('positions', 'capabilities')) {
                $positions = DB::table('positions')->select(['id', 'capabilities'])->get();

                foreach ($positions as $position) {
                    $capabilities = json_decode((string) ($position->capabilities ?? '[]'), true);
                    if (!is_array($capabilities) || !in_array(PositionCapability::APPROVE_REQUESTS, $capabilities, true)) {
                        continue;
                    }

                    $capabilities = array_values(array_unique(array_merge(
                        array_diff($capabilities, [PositionCapability::APPROVE_REQUESTS]),
                        array_keys($newCapabilities)
                    )));

                    DB::table('positions')
                        ->where('id', $position->id)
                        ->update([
                            'capabilities' => json_encode($capabilities),
                            'updated_at' => $now,
                        ]);
                }
            }

            if (Schema::hasTable('user_position_capability_overrides')) {
                $legacyOverrides = DB::table('user_position_capability_overrides')
                    ->where('capability_id', $oldCapabilityId)
                    ->get();

                foreach ($legacyOverrides as $override) {
                    foreach ($newCapabilityIds as $capabilityId) {
                        DB::table('user_position_capability_overrides')->updateOrInsert(
                            [
                                'user_id' => $override->user_id,
                                'capability_id' => $capabilityId,
                            ],
                            [
                                'effect' => $override->effect,
                                'reason' => $override->reason,
                                'expires_at' => $override->expires_at,
                                'created_by' => $override->created_by,
                                'created_at' => $override->created_at ?? $now,
                                'updated_at' => $now,
                            ]
                        );
                    }
                }
            }

            DB::table('position_capabilities')
                ->where('id', $oldCapabilityId)
                ->update([
                    'is_active' => false,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('position_capabilities')) {
            return;
        }

        $now = now();
        $oldCapabilityId = DB::table('position_capabilities')
            ->where('code', PositionCapability::APPROVE_REQUESTS)
            ->value('id');

        $newCapabilityIds = DB::table('position_capabilities')
            ->whereIn('code', [
                PositionCapability::APPROVE_USER_REQUESTS,
                PositionCapability::APPROVE_DEPARTMENT_REQUESTS,
                PositionCapability::APPROVE_SALARY_REQUESTS,
            ])
            ->pluck('id', 'code');

        if ($oldCapabilityId) {
            DB::table('position_capabilities')
                ->where('id', $oldCapabilityId)
                ->update([
                    'is_active' => true,
                    'updated_at' => $now,
                ]);

            if (Schema::hasTable('position_capability_position')) {
                $positionIds = DB::table('position_capability_position')
                    ->whereIn('capability_id', $newCapabilityIds->values())
                    ->pluck('position_id')
                    ->unique()
                    ->values();

                $legacyRows = $positionIds->map(fn ($positionId) => [
                    'position_id' => $positionId,
                    'capability_id' => $oldCapabilityId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if (!empty($legacyRows)) {
                    DB::table('position_capability_position')->insertOrIgnore($legacyRows);
                }

                DB::table('position_capability_position')
                    ->whereIn('capability_id', $newCapabilityIds->values())
                    ->delete();
            }

            if (Schema::hasTable('positions') && Schema::hasColumn('positions', 'capabilities')) {
                $positions = DB::table('positions')->select(['id', 'capabilities'])->get();

                foreach ($positions as $position) {
                    $capabilities = json_decode((string) ($position->capabilities ?? '[]'), true);
                    if (!is_array($capabilities)) {
                        continue;
                    }

                    $hasSplitCapability = collect(array_keys($newCapabilityIds->all()))
                        ->contains(fn ($code) => in_array($code, $capabilities, true));

                    if (!$hasSplitCapability) {
                        continue;
                    }

                    $capabilities = array_values(array_unique(array_merge(
                        array_diff($capabilities, array_keys($newCapabilityIds->all())),
                        [PositionCapability::APPROVE_REQUESTS]
                    )));

                    DB::table('positions')
                        ->where('id', $position->id)
                        ->update([
                            'capabilities' => json_encode($capabilities),
                            'updated_at' => $now,
                        ]);
                }
            }
        }

        if (Schema::hasTable('user_position_capability_overrides') && $newCapabilityIds->isNotEmpty()) {
            DB::table('user_position_capability_overrides')
                ->whereIn('capability_id', $newCapabilityIds->values())
                ->delete();
        }

        DB::table('position_capabilities')
            ->whereIn('code', array_keys($newCapabilityIds->all()))
            ->delete();
    }
};
