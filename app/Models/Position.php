<?php

namespace App\Models;

use App\Support\PositionRoleResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;

    protected static ?bool $hasCapabilityPivotTables = null;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'authority_level',
        'capabilities',
        'is_active'
    ];

    protected $casts = [
        'authority_level' => 'integer',
        'capabilities'    => 'array',
        'is_active'       => 'boolean',
    ];

    /**
     * Get the employees with this position.
     */
    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class);
    }

    public function capabilitiesCatalog(): BelongsToMany
    {
        return $this->belongsToMany(PositionCapability::class, 'position_capability_position', 'position_id', 'capability_id')
            ->where('position_capabilities.is_active', true)
            ->withTimestamps();
    }

    public function resolvedCapabilities(): array
    {
        if (!$this->canUseCapabilityPivot()) {
            return PositionRoleResolver::normalizeCapabilities(is_array($this->capabilities) ? $this->capabilities : []);
        }

        $codes = $this->relationLoaded('capabilitiesCatalog')
            ? $this->capabilitiesCatalog->pluck('code')->all()
            : $this->capabilitiesCatalog()->pluck('position_capabilities.code')->all();

        $codes = array_values(array_unique(array_filter($codes, fn ($code) => is_string($code) && $code !== '')));

        if (!empty($codes)) {
            return PositionRoleResolver::normalizeCapabilities($codes);
        }

        return PositionRoleResolver::normalizeCapabilities(is_array($this->capabilities) ? $this->capabilities : []);
    }

    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->resolvedCapabilities(), true);
    }

    public function syncCapabilityCodes(array $codes): void
    {
        if (!$this->canUseCapabilityPivot()) {
            return;
        }

        $codes = PositionRoleResolver::normalizeCapabilities($codes);

        $capabilityIds = PositionCapability::query()
            ->whereIn('code', $codes)
            ->pluck('id')
            ->all();

        $this->capabilitiesCatalog()->sync($capabilityIds);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeForDepartment($query, ?int $departmentId)
    {
        return $query->where(fn ($q) => $q->whereNull('department_id')->when($departmentId, fn ($q) => $q->orWhere('department_id', $departmentId)));
    }

    public function isGlobal(): bool
    {
        return $this->department_id === null;
    }

    private function canUseCapabilityPivot(): bool
    {
        if (self::$hasCapabilityPivotTables !== null) {
            return self::$hasCapabilityPivotTables;
        }

        self::$hasCapabilityPivotTables = Schema::hasTable('position_capabilities')
            && Schema::hasTable('position_capability_position');

        return self::$hasCapabilityPivotTables;
    }
}
