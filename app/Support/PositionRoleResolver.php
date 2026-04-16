<?php

namespace App\Support;

use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use Illuminate\Support\Facades\Schema;

class PositionRoleResolver
{
    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_HR = 'hr';
    public const ROLE_ADMIN = 'admin';
    private static ?array $allowedCapabilityCache = null;

    /**
     * Capability bắt buộc tối thiểu authority level bao nhiêu.
     */
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

    /**
     * Quyền kéo theo quyền (implies).
     */
    private const IMPLIED_CAPABILITIES = [
        PositionCapability::MANAGE_SALARY => [
            PositionCapability::VIEW_SALARY,
        ],
        PositionCapability::EXPORT_ATTENDANCE => [
            PositionCapability::VIEW_ALL_ATTENDANCE,
        ],
        PositionCapability::MANAGE_PROJECT_ROLES => [
            PositionCapability::MANAGE_PROJECT_MEMBERS,
            PositionCapability::MANAGE_PROJECTS,
        ],
        PositionCapability::EXPORT_REPORTS => [
            PositionCapability::VIEW_REPORTS,
        ],
    ];

    public static function resolveMinimumRole(?Position $position): string
    {
        if (!$position) {
            return self::ROLE_EMPLOYEE;
        }

        $capabilities = self::normalizeCapabilities(
            method_exists($position, 'resolvedCapabilities')
                ? $position->resolvedCapabilities()
                : (is_array($position->capabilities) ? $position->capabilities : [])
        );
        $authorityLevel = (int) ($position->authority_level ?? 0);

        $roleByLevel = self::resolveMinimumRoleByAuthorityLevel($authorityLevel);
        $roleByCapability = self::resolveMinimumRoleByAuthorityLevel(
            self::resolveMinimumAuthorityLevelFromCapabilities($capabilities)
        );

        return self::roleRank($roleByCapability) > self::roleRank($roleByLevel)
            ? $roleByCapability
            : $roleByLevel;
    }

    public static function roleRank(string $role): int
    {
        return match ($role) {
            self::ROLE_ADMIN => 3,
            self::ROLE_HR => 2,
            default => 1,
        };
    }

    public static function allowsRoleForPosition(string $role, ?Position $position): bool
    {
        $minimumRole = self::resolveMinimumRole($position);

        return self::roleRank($role) >= self::roleRank($minimumRole);
    }

    public static function resolveMinimumRoleByAuthorityLevel(int $authorityLevel): string
    {
        if ($authorityLevel >= 5) {
            return self::ROLE_ADMIN;
        }

        if ($authorityLevel >= 4) {
            return self::ROLE_HR;
        }

        return self::ROLE_EMPLOYEE;
    }

    public static function normalizeCapabilities(array $capabilities): array
    {
        $allowed = array_fill_keys(self::allowedCapabilities(), true);
        $normalized = [];

        foreach ($capabilities as $capability) {
            if (is_string($capability) && isset($allowed[$capability])) {
                $normalized[$capability] = true;
            }
        }

        // Áp dụng luật kéo theo nhiều vòng đến khi ổn định.
        do {
            $changed = false;
            foreach (array_keys($normalized) as $capability) {
                foreach (self::IMPLIED_CAPABILITIES[$capability] ?? [] as $implied) {
                    if (!isset($normalized[$implied])) {
                        $normalized[$implied] = true;
                        $changed = true;
                    }
                }
            }
        } while ($changed);

        return array_values(array_keys($normalized));
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
}
