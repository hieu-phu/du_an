<?php

namespace App\Support;

use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use Illuminate\Support\Facades\Schema;

class PositionRoleResolver
{
    private static ?array $allowedCapabilityCache = null;

    private const CAPABILITY_MIN_AUTHORITY = [
        PositionCapability::MANAGE_POSITIONS => 5,
        PositionCapability::VIEW_ACTIVITY_LOGS => 5,
        PositionCapability::SIGN_DOCUMENTS => 5,
        PositionCapability::MANAGE_EMPLOYEES => 4,
        PositionCapability::MANAGE_SALARY => 4,
        PositionCapability::VIEW_SALARY => 4,
        PositionCapability::MANAGE_DEPARTMENTS => 4,
        PositionCapability::TRANSFER_EMPLOYEE => 4,
        PositionCapability::APPROVE_ATTENDANCE => 4,
        PositionCapability::APPROVE_LEAVE => 4,
        PositionCapability::APPROVE_REQUESTS => 4,
    ];

    public static function normalizeCapabilities(array $capabilities): array
    {
        $allowed = array_fill_keys(self::allowedCapabilities(), true);
        $normalized = [];

        foreach ($capabilities as $capability) {
            if (is_string($capability) && isset($allowed[$capability])) {
                $normalized[$capability] = true;
            }
        }

        return array_values(array_keys($normalized));
    }

    public static function resolveMinimumAuthorityLevelFromCapabilities(array $capabilities): int
    {
        $required = 1;

        foreach (self::normalizeCapabilities($capabilities) as $capability) {
            $required = max($required, (int) (self::CAPABILITY_MIN_AUTHORITY[$capability] ?? 1));
        }

        return $required;
    }

    public static function normalizePositionPayload(array $data): array
    {
        $capabilities = self::normalizeCapabilities((array) ($data['capabilities'] ?? []));
        $authorityLevel = (int) ($data['authority_level'] ?? 0);

        if ($authorityLevel <= 0) {
            $authorityLevel = 1;
        }

        $authorityLevel = max($authorityLevel, self::resolveMinimumAuthorityLevelFromCapabilities($capabilities));

        $data['capabilities'] = $capabilities;
        $data['authority_level'] = $authorityLevel;

        return $data;
    }

    private static function allowedCapabilities(): array
    {
        if (self::$allowedCapabilityCache !== null) {
            return self::$allowedCapabilityCache;
        }

        $keys = PositionCapability::all();

        if (Schema::hasTable('position_capabilities')) {
            $dbKeys = PositionCapabilityModel::query()->pluck('code')->all();
            $keys = array_values(array_unique(array_merge($keys, $dbKeys)));
        }

        self::$allowedCapabilityCache = $keys;

        return $keys;
    }
}
