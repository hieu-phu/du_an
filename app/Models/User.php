<?php

namespace App\Models;

use App\Models\ProjectMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected ?array $positionCapabilityDecisionCache = null;
    protected static ?bool $hasUserCapabilityOverridesTable = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'email_verified_at',
        'username',
        'phone',
        'password',
        'address',
        'avatar',
        'thumbnail',
        'slug',
        'creater_id',
        'status',
        'zalo_verified',
        'zalo_verified_at',
        'zalo_user_id',
        'last_login_at',
        'last_login_ip',
        'name',
        'is_employee'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'zalo_verified' => 'boolean',
            'zalo_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function socialProviders(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the creator of the user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creater_id');
    }

    /**
     * Get the users created by this user.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'creater_id');
    }

    /**
     * Get the sessions for the user.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function loginOtps(): HasMany
    {
        return $this->hasMany(LoginOtp::class);
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function positionCapabilityOverrides(): HasMany
    {
        return $this->hasMany(UserPositionCapabilityOverride::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include blocked users.
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Scope a query to only include pending users.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Kiểm tra xem user có quyền hạn nghiệp vụ này không,
     * dựa theo chức vụ của nhân viên đó.
     * Admin luôn có mọi quyền.
     */
    public function hasPositionCapability(string $capability): bool
    {
        if ($capability === '') {
            return false;
        }

        if ($this->positionCapabilityDecisionCache === null) {
            $this->positionCapabilityDecisionCache = $this->buildPositionCapabilityDecisionCache();
        }

        return (bool) ($this->positionCapabilityDecisionCache[$capability] ?? false);
    }

    public function hasAnyPositionCapability(array $capabilities): bool
    {
        foreach ($capabilities as $capability) {
            if (is_string($capability) && $capability !== '' && $this->hasPositionCapability($capability)) {
                return true;
            }
        }

        return false;
    }

    private function buildPositionCapabilityDecisionCache(): array
    {
        $profile = $this->relationLoaded('employeeProfile')
            ? $this->employeeProfile
            : $this->employeeProfile()->with('position.capabilitiesCatalog')->first();

        if (!$profile || !$profile->position || !$profile->position->is_active) {
            return [];
        }

        $decision = array_fill_keys($profile->position->resolvedCapabilities(), true);

        if ($this->canUseCapabilityOverrideTable()) {
            $overrides = $this->positionCapabilityOverrides()
                ->with('capability:id,code')
                ->where(function (Builder $query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->get();

            foreach ($overrides as $override) {
                $code = $override->capability?->code;
                if (!is_string($code) || $code === '') {
                    continue;
                }
                if ($override->effect === 'deny' && ($decision[$code] ?? false)) {
                    $decision[$code] = false;
                }
            }
        }

        return $decision;
    }

    private function canUseCapabilityOverrideTable(): bool
    {
        if (self::$hasUserCapabilityOverridesTable !== null) {
            return self::$hasUserCapabilityOverridesTable;
        }

        self::$hasUserCapabilityOverridesTable = Schema::hasTable('user_position_capability_overrides');

        return self::$hasUserCapabilityOverridesTable;
    }

    public function hasActiveProjectMembership(): bool
    {
        $profileId = $this->employeeProfile?->id;

        if (!$profileId) {
            $profileId = (int) $this->employeeProfile()->value('id');
        }

        if ($profileId <= 0) {
            return false;
        }

        return ProjectMember::query()
            ->where('employee_profile_id', $profileId)
            ->where('is_active', true)
            ->exists();
    }
}
