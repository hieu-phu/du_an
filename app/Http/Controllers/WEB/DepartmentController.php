<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\DepartmentService;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = $this->departmentService->index();
        $users = User::all(['id', 'name']); // For manager selection

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên phòng ban là bắt buộc.',
            'name.max' => 'Tên phòng ban không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên phòng ban này đã tồn tại.',
            'manager_user_id.exists' => 'Trưởng phòng không hợp lệ.',
        ]);

        $this->departmentService->store($validated);

        return redirect()->back()->with('success', 'Phòng ban đã được tạo thành công.');
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $department = \App\Models\Department::withCount('employeeProfiles')->findOrFail($id);

        if ($department->employee_profiles_count > 0) {
            return redirect()->back()->withErrors([
                'department' => "Phòng ban \"{$department->name}\" đang có {$department->employee_profiles_count} nhân viên. Không thể chỉnh sửa khi đang được sử dụng."
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên phòng ban là bắt buộc.',
            'name.max' => 'Tên phòng ban không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên phòng ban này đã tồn tại.',
            'manager_user_id.exists' => 'Trưởng phòng không hợp lệ.',
        ]);

        $this->departmentService->update($id, $validated);

        return redirect()->back()->with('success', 'Phòng ban đã được cập nhật thành công.');
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $this->departmentService->delete($id);

        return redirect()->back()->with('success', 'Phòng ban đã được xóa thành công.');
    }
}
