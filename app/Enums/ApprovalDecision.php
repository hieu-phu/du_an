<?php

namespace App\Enums;

enum ApprovalDecision: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Đã duyệt',
            self::REJECTED => 'Từ chối',
            self::CANCELLED => 'Đã hủy',
        };
    }

    public function mailVerb(): string
    {
        return match ($this) {
            self::APPROVED => 'đã được duyệt',
            self::REJECTED => 'đã bị từ chối',
            self::CANCELLED => 'đã bị hủy',
        };
    }
}
