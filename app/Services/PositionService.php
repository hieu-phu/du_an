<?php

namespace App\Services;

use App\Repositories\PositionRepository;

class PositionService extends BaseService
{
    public function __construct(
        protected PositionRepository $positionRepository
    ) {}

    public function index()
    {
        return $this->positionRepository->getAll();
    }

    public function store(array $data)
    {
        return $this->positionRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->positionRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->positionRepository->delete($id);
    }
}
