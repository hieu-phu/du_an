<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\PositionService;
use App\Support\PositionCapability;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function __construct(
        protected PositionService $positionService
    ) {}

    public function index(Request $request)
    {
        $positions = $this->positionService->index(
            search: $request->input('search'),
            status: $request->input('status'),
        );

        return Inertia::render('Positions/Index', [
            'positions' => $positions,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:positions,name',
            'description'     => 'nullable|string',
            'authority_level' => 'nullable|integer|min:1|max:10',
            'capabilities'    => 'nullable|array',
            'capabilities.*'  => ['string', Rule::in(PositionCapability::all())],
            'is_active'       => 'boolean',
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max'      => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique'   => 'Tên chức vụ này đã tồn tại.',
            'authority_level.integer' => 'Mức quyền hạn phải là số nguyên.',
            'authority_level.min'     => 'Mức quyền hạn tối thiểu là 1.',
            'authority_level.max'     => 'Mức quyền hạn tối đa là 10.',
        ]);

        $this->positionService->store($validated);

        return redirect()->back()->with('success', 'Chức vụ đã được tạo thành công.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:positions,name,' . $id,
            'description'     => 'nullable|string',
            'authority_level' => 'nullable|integer|min:1|max:10',
            'capabilities'    => 'nullable|array',
            'capabilities.*'  => ['string', Rule::in(PositionCapability::all())],
            'is_active'       => 'boolean',
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max'      => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique'   => 'Tên chức vụ này đã tồn tại.',
            'authority_level.integer' => 'Mức quyền hạn phải là số nguyên.',
            'authority_level.min'     => 'Mức quyền hạn tối thiểu là 1.',
            'authority_level.max'     => 'Mức quyền hạn tối đa là 10.',
        ]);

        $this->positionService->update($id, $validated);

        return redirect()->back()->with('success', 'Chức vụ đã được cập nhật thành công.');
    }

    public function toggleStatus($id)
    {
        $this->positionService->toggleStatus($id);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái chức vụ.');
    }

    public function destroy($id)
    {
        $position = Position::withCount('employeeProfiles')->findOrFail($id);

        if ($position->employee_profiles_count > 0) {
            return redirect()->back()->withErrors([
                'position' => "Chức vụ \"{$position->name}\" đang có {$position->employee_profiles_count} nhân viên sử dụng. Không thể xóa.",
            ]);
        }

        $this->positionService->delete($id);

        return redirect()->back()->with('success', 'Chức vụ đã được xóa thành công.');
    }
}
