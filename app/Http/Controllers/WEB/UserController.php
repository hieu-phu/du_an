<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page']);
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getListPaginated($filters, $perPage);

        return Inertia::render('User/Index', [
            'users'   => $users,
            'filters' => $filters,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->createUser(
                $request->validated(),
                $request->file('avatar')
            );

            return redirect()->back()->with('success', 'Tạo nhân sự thành công!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->updateUser(
                $user,
                $request->validated(),
                $request->file('avatar')
            );

            return redirect()->back()->with('success', 'Cập nhật nhân sự thành công!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Cập nhật thất bại: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            $this->userService->toggleStatus($user);

            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
