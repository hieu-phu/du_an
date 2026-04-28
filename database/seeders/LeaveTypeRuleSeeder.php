<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeRuleSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'ANNUAL',
                'name' => 'Nghỉ hàng năm',
                'is_paid' => true,
                'deducts_balance' => true,
                'requires_attachment' => false,
                'annual_quota' => 12,
                'prorate_by_hire_date' => true,
                'description' => 'Nghỉ phép năm có hưởng lương. +1 ngày mỗi 5 năm làm việc.',
            ],
            [
                'code' => 'MARRIAGE',
                'name' => 'Nghỉ kết hôn',
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'annual_quota' => 3,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ kết hôn (3 ngày).',
            ],
            [
                'code' => 'CHILD_MARRIAGE',
                'name' => 'Nghỉ con kết hôn',
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'annual_quota' => 1,
                'prorate_by_hire_date' => false,
                'description' => 'Con đẻ, con nuôi kết hôn (1 ngày).',
            ],
            [
                'code' => 'BEREAVEMENT',
                'name' => 'Nghỉ hiếu',
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'annual_quota' => 3,
                'prorate_by_hire_date' => false,
                'description' => 'Tứ thân phụ mẫu, vợ/chồng, con chết (3 ngày).',
            ],
            [
                'code' => 'NGHI_OM',
                'name' => 'Nghỉ bệnh',
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => false,
                'annual_quota' => 0,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ bệnh. Từ 3 ngày trở lên yêu cầu giấy xác nhận của bác sĩ.',
            ],
            [
                'code' => 'UNPAID',
                'name' => 'Nghỉ không lương',
                'is_paid' => false,
                'deducts_balance' => false,
                'requires_attachment' => false,
                'annual_quota' => 0,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ việc riêng khác không hưởng lương.',
            ],
            [
                'code' => 'MATERNITY',
                'name' => 'Nghỉ thai sản',
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'annual_quota' => 0,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ thai sản theo quy định BHXH.',
            ],
        ];

        foreach ($types as $type) {
            LeaveType::query()->updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
