<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AuthorityLevel;
use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use App\Models\User;
use App\Services\PositionService;
use App\Support\PositionCapability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PositionController extends Controller
{
    private const SYSTEM_OWNER_EMAIL = 'gtvbehieu@gmail.com';
    public function __construct(
        protected PositionService $positionService
    ) {}

    public function index(Request $request)
    {
        $positions = $this->positionService->index(
            search: $request->input('search'),
            status: $request->input('status'),
        );
        $actor = $request->user();
        $actorLevel = $actor ? $this->resolveActorAuthorityLevel($actor) : 0;
        $actorPositionId = (int) ($actor?->employeeProfile?->position_id ?? 0);

        $positions = $positions->map(function (Position $position) use ($actorLevel, $actorPositionId) {
            $canManage = ((int) ($position->authority_level ?? 0) <= $actorLevel)
                && ((int) $position->id !== $actorPositionId);

            $position->setAttribute('can_edit', $canManage);
            $position->setAttribute('can_toggle', $canManage);

            return $position;
        });

        return Inertia::render('Positions/Index', [
            'positions' => $positions,
            'filters' => $request->only(['search', 'status']),
            'capabilityOptions' => $this->capabilityOptions(),
            'canCreateCustomCapabilities' => false,
            'authorityLevels' => $this->authorityLevelOptions(),
            'authorityLevelCatalog' => $this->authorityLevelCatalog(),
        ]);
    }

    public function store(Request $request)
    {
        $allowedCapabilities = $this->allowedCapabilityKeys();
        $allowedAuthorityLevels = $this->allowedAuthorityLevelValues();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
            'description' => ['nullable', 'string'],
            'authority_level' => ['required', 'integer', Rule::in($allowedAuthorityLevels)],
            'capabilities' => ['nullable', 'array'],
            'capabilities.*' => ['string', Rule::in($allowedCapabilities)],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max' => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên chức vụ này đã tồn tại.',
            'authority_level.required' => 'Vui lòng chọn mức quyền hạn.',
            'authority_level.integer' => 'Mức quyền hạn phải là số nguyên.',
            'authority_level.in' => 'Mức quyền hạn không hợp lệ.',
        ]);
        $actorLevel = $this->resolveActorAuthorityLevel($request->user());
        if (!$this->isSystemOwner($request->user()) && (int) ($validated['authority_level'] ?? 0) >= $actorLevel) {
            throw ValidationException::withMessages([
                'authority_level' => 'Bạn chỉ được tạo chức vụ có mức quyền hạn thấp hơn cấp bậc hiện tại của bạn.',
            ]);
        }

        // TẮT kiểm tra quyền gán tạm thời để có thể thiết lập phân quyền ban đầu
        // $capabilities = $validated['capabilities'] ?? [];
        // $actor = $request->user();
        // foreach ($capabilities as $cap) {
        //     if (!$actor->hasPositionCapability($cap)) {
        //         throw ValidationException::withMessages([
        //             'capabilities' => "Bạn không có quyền: {$cap} nên không thể gán quyền này.",
        //         ]);
        //     }
        // }

        $this->positionService->store($validated);

        return redirect()->back()->with('success', 'Chức vụ đã được tạo thành công.');
    }

    public function update(Request $request, int $id)
    {
        $position = Position::query()->findOrFail($id);
        $this->assertManageablePosition($request->user(), $position);

        $allowedCapabilities = $this->allowedCapabilityKeys();
        $allowedAuthorityLevels = $this->allowedAuthorityLevelValues();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:positions,name,{$id}"],
            'description' => ['nullable', 'string'],
            'authority_level' => ['required', 'integer', Rule::in($allowedAuthorityLevels)],
            'capabilities' => ['nullable', 'array'],
            'capabilities.*' => ['string', Rule::in($allowedCapabilities)],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max' => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên chức vụ này đã tồn tại.',
            'authority_level.required' => 'Vui lòng chọn mức quyền hạn.',
            'authority_level.integer' => 'Mức quyền hạn phải là số nguyên.',
            'authority_level.in' => 'Mức quyền hạn không `hợp lệ.',
        ]);

        $actorLevel = $this->resolveActorAuthorityLevel($request->user());
        if (!$this->isSystemOwner($request->user()) && (int) ($validated['authority_level'] ?? 0) >= $actorLevel) {
            throw ValidationException::withMessages([
                'authority_level' => 'Bạn chỉ được cập nhật chức vụ với mức quyền hạn thấp hơn cấp bậc hiện tại của bạn.',
            ]);
        }

        // TẮT kiểm tra quyền gán tạm thời để có thể thiết lập phân quyền ban đầu
        // $capabilities = $validated['capabilities'] ?? [];
        // $actor = $request->user();
        // foreach ($capabilities as $cap) {
        //     if (!$actor->hasPositionCapability($cap)) {
        //         throw ValidationException::withMessages([
        //             'capabilities' => "Bạn không có quyền: {$cap} nên không thể gán quyền này.",
        //         ]);
        //     }
        // }

        $this->positionService->update($id, $validated);

        return redirect()->back()->with('success', 'Chức vụ đã được cập nhật thành công.');
    }

    public function toggleStatus(Request $request, int $id)
    {
        $position = Position::query()->findOrFail($id);
        $this->assertManageablePosition($request->user(), $position);

        $this->positionService->toggleStatus($id);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái chức vụ.');
    }

    public function destroy(Request $request, int $id)
    {
        $position = Position::withCount('employeeProfiles')->findOrFail($id);
        $this->assertManageablePosition($request->user(), $position);

        if ($position->employee_profiles_count > 0) {
            return redirect()->back()->withErrors([
                'position' => "Chức vụ \"{$position->name}\" đang có {$position->employee_profiles_count} nhân viên sử dụng. Không thể xóa.",
            ]);
        }

        $this->positionService->delete($id);

        return redirect()->back()->with('success', 'Chức vụ đã được xóa thành công.');
    }

    public function storeCapability(Request $request)
    {
        abort(403, 'Chuc nang them quyen tuy chinh da duoc tat.');

        if (!Schema::hasTable('position_capabilities')) {
            return back()->withErrors(['error' => 'Bảng danh mục quyền chưa sẵn sàng. Vui lòng chạy migrate.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập tên quyền.',
        ]);

        $baseCode = Str::of((string) $validated['name'])
            ->ascii()
            ->lower()
            ->slug('_')
            ->toString();

        if ($baseCode === '') {
            $baseCode = 'custom_capability';
        }
        $code = $baseCode;
        $suffix = 2;
        while (PositionCapabilityModel::query()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . $suffix;
            $suffix++;
        }

        PositionCapabilityModel::query()->create([
            'code' => $code,
            'name' => $validated['name'],
            'module' => $validated['module'] ?: 'custom',
            'description' => $validated['description'] ?? null,
            'is_system' => false,
            'is_active' => true,
        ]);

        return back()->with('success', "Đã thêm quyền mới thành công (mã: {$code}).");
    }

    public function storeAuthorityLevel(Request $request)
    {
        if ($this->resolveActorAuthorityLevel($request->user()) < 10) {
            return back()->withErrors(['error' => 'Chỉ tài khoản (mức 10) mới được phép quản lý danh mục mức quyền hạn.']);
        }

        if (!Schema::hasTable('authority_levels')) {
            return back()->withErrors(['error' => 'Bang muc quyen han chua san sang. Vui long chay migrate.']);
        }

        $validated = $request->validate([
            'rank' => ['required', 'integer', 'min:1', 'max:32767', 'unique:authority_levels,rank'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['boolean'],
        ], [
            'rank.required' => 'Vui long nhap so thu bac.',
            'rank.unique' => 'So thu bac nay da ton tai.',
            'name.required' => 'Vui long nhap ten muc quyen han.',
        ]);

        $rank = (int) $validated['rank'];

        AuthorityLevel::query()->create([
            'rank' => $rank,
            'name' => (string) $validated['name'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Da them muc quyen han moi.');
    }

    public function toggleAuthorityLevel(AuthorityLevel $authorityLevel)
    {
        if ($this->resolveActorAuthorityLevel(request()->user()) < 10) {
            return back()->withErrors(['error' => 'Chỉ tài khoản (mức 10) mới được phép quản lý danh mục mức quyền hạn.']);
        }

        $next = !$authorityLevel->is_active;

        if (!$next) {
            $inUse = Position::query()
                ->where('authority_level', (int) $authorityLevel->rank)
                ->exists();

            if ($inUse) {
                return back()->withErrors([
                    'authority_level' => 'Khong the khoa muc nay vi dang co chuc vu su dung.',
                ]);
            }
        }

        $authorityLevel->update([
            'is_active' => $next,
        ]);

        return back()->with('success', $next ? 'Da mo muc quyen han.' : 'Da khoa muc quyen han.');
    }

    private function allowedCapabilityKeys(): array
    {
        $keys = PositionCapability::all();

        if (Schema::hasTable('position_capabilities')) {
            $dbKeys = PositionCapabilityModel::query()->pluck('code')->all();
            $keys = array_values(array_unique(array_merge($keys, $dbKeys)));
        }

        return $keys;
    }

    private function capabilityOptions(): array
    {
        if (Schema::hasTable('position_capabilities')) {
            return PositionCapabilityModel::query()
                ->where('is_active', true)
                ->orderBy('module')
                ->orderBy('name')
                ->get(['code', 'name', 'module', 'description'])
                ->map(fn ($item) => [
                    'key' => $item->code,
                    'label' => $item->name,
                    'desc' => $item->description,
                    'module' => $item->module ?: 'custom',
                ])
                ->values()
                ->all();
        }

        return collect(PositionCapability::definitions())
            ->map(fn ($meta, $code) => [
                'key' => $code,
                'label' => (string) ($meta['name'] ?? $code),
                'desc' => (string) ($meta['description'] ?? ''),
                'module' => (string) ($meta['module'] ?? 'custom'),
            ])
            ->values()
            ->all();
    }

    private function authorityLevelOptions(): array
    {
        if (Schema::hasTable('authority_levels')) {
            return AuthorityLevel::query()
                ->where('is_active', true)
                ->orderBy('rank')
                ->get(['rank', 'name'])
                ->map(fn ($item) => [
                    'value' => (int) $item->rank,
                    'label' => (string) $item->name,
                ])
                ->values()
                ->all();
        }
        return [
            ['value' => 1, 'label' => 'Mức 1 - Nhân viên'],
            ['value' => 2, 'label' => 'Mức 2 - Tổ phó / Senior'],
            ['value' => 3, 'label' => 'Mức 3 - Trưởng nhóm'],
            ['value' => 4, 'label' => 'Mức 4 - Trưởng phòng'],
            ['value' => 5, 'label' => 'Mức 5 - Giám đốc / Quản lý cao'],
            ['value' => 6, 'label' => 'Mức 6 - Giám đốc khối / VP'],
            ['value' => 7, 'label' => 'Mức 7 - Phó tổng giám đốc'],
            ['value' => 8, 'label' => 'Mức 8 - Tổng giám đốc'],
            ['value' => 9, 'label' => 'Mức 9 - Hội đồng quản trị'],
            ['value' => 10, 'label' => 'Mức 10 - Quản trị hệ thống (System Admin)'],
        ];
    }
    private function allowedAuthorityLevelValues(): array
    {
        $values = collect($this->authorityLevelOptions())
            ->pluck('value')
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        return empty($values) ? [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] : $values;
    }

    private function authorityLevelCatalog(): array
    {
        if (Schema::hasTable('authority_levels')) {
            return AuthorityLevel::query()
                ->orderBy('rank')
                ->get(['id', 'rank', 'name', 'is_active'])
                ->map(fn ($item) => [
                    'id' => (int) $item->id,
                    'rank' => (int) $item->rank,
                    'name' => (string) $item->name,
                    'is_active' => (bool) $item->is_active,
                ])
                ->values()
                ->all();
        }

        return [];
    }

    private function assertManageablePosition(?User $actor, Position $position): void
    {
        if (!$actor) {
            abort(403);
        }

        if ($this->isSystemOwner($actor)) {
            return;
        }

        $actor->loadMissing('employeeProfile.position');
        $actorPositionId = (int) ($actor->employeeProfile?->position_id ?? 0);

        if ($actorPositionId > 0 && $actorPositionId === (int) $position->id) {
            throw ValidationException::withMessages([
                'position' => 'Ban khong duoc sua chuc vu cua chinh minh.',
            ]);
        }

        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $targetLevel = (int) ($position->authority_level ?? 0);

        if ($targetLevel > $actorLevel) {
            throw ValidationException::withMessages([
                'position' => 'Ban khong duoc sua chuc vu co muc quyen han cao hon minh.',
            ]);
        }
    }

    private function resolveActorAuthorityLevel(User $actor): int
    {
        $actor->loadMissing('employeeProfile.position');

        if ($this->isSystemOwner($actor)) {
            return $this->resolveMaxAuthorityLevel();
        }

        $positionLevel = (int) ($actor->employeeProfile?->position?->authority_level ?? 0);

        if ($positionLevel > 0) {
            return $positionLevel;
        }

        if ($actor->hasPositionCapability(PositionCapability::MANAGE_POSITIONS)) {
            return $this->resolveMaxAuthorityLevel();
        }

        return 1;
    }

    private function resolveMaxAuthorityLevel(): int
    {
        if (Schema::hasTable('authority_levels')) {
            $max = (int) (AuthorityLevel::query()
                ->where('is_active', true)
                ->max('rank') ?? 0);

            if ($max > 0) {
                return $max;
            }
        }

        return 10;
    }

    private function isSystemOwner(?User $actor): bool
    {
        return $actor !== null
            && strcasecmp((string) $actor->email, self::SYSTEM_OWNER_EMAIL) === 0;
    }
}
