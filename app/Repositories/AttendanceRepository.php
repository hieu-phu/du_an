<?php

namespace App\Repositories;

use App\Models\AttendanceRecord;

class AttendanceRepository extends BaseRepository
{
    public function __construct(AttendanceRecord $model)
    {
        parent::__construct($model);
    }
}
