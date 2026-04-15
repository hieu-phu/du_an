<?php

namespace App\Services;

use App\Repositories\PositionRepository;

class PositionService extends BaseService
{
    public function __construct(
        protected PositionRepository $positionRepository
    ) {}

    public function index(?string $search = null, ?string $status = null)
    {
        return $this->positionRepository->getAllWithEmployeeCount($search, $status);
    }

    public function store(array $data)
    {
        $position = $this->positionRepository->create($data);

        $this->logActivity('create', "Đã tạo chức vụ mới: {$position->name}", $position->id);

        return $position;
    }

    public function update($id, array $data)
    {
        $position = $this->positionRepository->find($id);
        $oldName = $position->name;
        $oldCaps = $position->capabilities ?? [];
        $newCaps = $data['capabilities'] ?? [];

        $result = $this->positionRepository->update($id, $data);
        $position->refresh();

        $changes = [];

        if ($oldName !== $position->name) {
            $changes[] = "Đổi tên từ '{$oldName}' sang '{$position->name}'";
        }

        // So sánh quyền hạn
        $added = array_diff($newCaps ?? [], $oldCaps ?? []);
        $removed = array_diff($oldCaps ?? [], $newCaps ?? []);

        if (!empty($added)) {
            $changes[] = "Cấp thêm quyền: " . implode(', ', $added);
        }
        if (!empty($removed)) {
            $changes[] = "Thu hồi quyền: " . implode(', ', $removed);
        }

        $description = "Cập nhật chức vụ '{$position->name}'" . (!empty($changes) ? ": " . implode('; ', $changes) : "");

        $this->logActivity('update', $description, $id);

        return $result;
    }

    public function toggleStatus($id)
    {
        $position = $this->positionRepository->find($id);
        $newStatus = !$position->is_active;

        $result = $this->positionRepository->update($id, ['is_active' => $newStatus]);
        $statusText = $newStatus ? 'kích hoạt' : 'tạm khóa';

        $this->logActivity('toggle_status', "Đã {$statusText} chức vụ: {$position->name}", $id);

        return $result;
    }

    public function delete($id)
    {
        $position = $this->positionRepository->find($id);
        $name = $position->name;

        $result = $this->positionRepository->delete($id);

        $this->logActivity('delete', "Đã xóa chức vụ: {$name}", $id);

        return $result;
    }

    protected function logActivity(string $action, string $description, $id = null): void
    {
        $this->audit('positions', $action, $description, 'positions', $id);
    }
}
