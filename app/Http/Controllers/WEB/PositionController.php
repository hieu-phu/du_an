<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\PositionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function __construct(
        protected PositionService $positionService
    ) {}

    public function index()
    {
        $positions = $this->positionService->index();

        return Inertia::render('Positions/Index', [
            'positions' => $positions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max' => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên chức vụ này đã tồn tại.',
        ]);

        $this->positionService->store($validated);

        return redirect()->back()->with('success', 'Chức vụ đã được tạo thành công.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên chức vụ là bắt buộc.',
            'name.max' => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên chức vụ này đã tồn tại.',
        ]);

        $this->positionService->update($id, $validated);

        return redirect()->back()->with('success', 'Chức vụ đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $this->positionService->delete($id);

        return redirect()->back()->with('success', 'Chức vụ đã được xóa thành công.');
    }
}
