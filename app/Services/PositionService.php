<?php

namespace App\Services;

use App\Repositories\PositionRepository;
use App\Support\PositionRoleResolver;

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
        $data = PositionRoleResolver::normalizePositionPayload($data);
        $position = $this->positionRepository->create($data);
        $position->syncCapabilityCodes($data['capabilities'] ?? []);

        $this->logActivity('create', "Da tao chức vụ moi: {$position->name}", $position->id);

        return $position;
    }

    public function update($id, array $data)
    {
        $data = PositionRoleResolver::normalizePositionPayload($data);
        $position = $this->positionRepository->getByIdOrFail((int) $id);
        $oldName = $position->name;
        $oldCaps = $position->resolvedCapabilities();
        $newCaps = $data['capabilities'] ?? [];

        $result = $this->positionRepository->update($id, $data);
        $position->refresh();
        $position->syncCapabilityCodes($newCaps);

        $changes = [];

        if ($oldName !== $position->name) {
            $changes[] = "Doi ten tu '{$oldName}' sang '{$position->name}'";
        }

        $added = array_diff($newCaps ?? [], $oldCaps ?? []);
        $removed = array_diff($oldCaps ?? [], $newCaps ?? []);

        if (!empty($added)) {
            $changes[] = 'Cap them quyen: ' . implode(', ', $added);
        }
        if (!empty($removed)) {
            $changes[] = 'Thu hoi quyen: ' . implode(', ', $removed);
        }

        $description = "Cap nhat chức vụ '{$position->name}'" . (!empty($changes) ? ': ' . implode('; ', $changes) : '');

        $this->logActivity('update', $description, $id);

        return $result;
    }

    public function toggleStatus($id)
    {
        $position = $this->positionRepository->getByIdOrFail((int) $id);
        $newStatus = !$position->is_active;

        $result = $this->positionRepository->update($id, ['is_active' => $newStatus]);
        $statusText = $newStatus ? 'kich hoat' : 'tam khoa';

        $this->logActivity('toggle_status', "Da {$statusText} chức vụ: {$position->name}", $id);

        return $result;
    }

    public function delete($id)
    {
        $position = $this->positionRepository->getByIdOrFail((int) $id);
        $name = $position->name;

        $result = $this->positionRepository->delete($id);

        $this->logActivity('delete', "Da xoa chức vụ: {$name}", $id);

        return $result;
    }

    protected function logActivity(string $action, string $description, $id = null): void
    {
        $this->audit('positions', $action, $description, 'positions', $id);
    }
}


