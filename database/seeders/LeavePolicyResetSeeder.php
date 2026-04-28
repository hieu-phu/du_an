<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeavePolicyResetSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to safely truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing leave types and balances
        LeaveType::query()->truncate();
        EmployeeLeaveBalance::query()->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            [
                'code' => 'ANNUAL',
                'name' => 'Nghỉ hàng năm',
                'annual_quota' => 12,
                'is_paid' => true,
                'deducts_balance' => true,
                'requires_attachment' => false,
                'prorate_by_hire_date' => true,
                'description' => 'Người lao động có đủ 12 tháng làm việc được nghỉ 12 ngày/năm. Cứ 05 năm làm việc được nghỉ thêm 01 ngày.',
            ],
            [
                'code' => 'MARRIAGE',
                'name' => 'Nghỉ kết hôn',
                'annual_quota' => 3,
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'prorate_by_hire_date' => false,
                'description' => 'Bản thân kết hôn: nghỉ 03 ngày, hưởng nguyên lương.',
            ],
            [
                'code' => 'CHILD_MARRIAGE',
                'name' => 'Nghỉ con kết hôn',
                'annual_quota' => 1,
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'prorate_by_hire_date' => false,
                'description' => 'Con đẻ, con nuôi kết hôn: nghỉ 01 ngày, hưởng nguyên lương.',
            ],
            [
                'code' => 'BEREAVEMENT',
                'name' => 'Nghỉ tang chế',
                'annual_quota' => 3,
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'prorate_by_hire_date' => false,
                'description' => 'Bố đẻ, mẹ đẻ, bố vợ, mẹ vợ (chồng), vợ hoặc chồng, con đẻ, con nuôi chết: nghỉ 03 ngày, hưởng nguyên lương.',
            ],
            [
                'code' => 'NGHI_OM',
                'name' => 'Nghỉ bệnh (Ốm đau)',
                'annual_quota' => 0,
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => false,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ bệnh hưởng chế độ. Nghỉ từ 03 ngày trở lên bắt buộc có giấy xác nhận của bác sĩ/bệnh viện.',
            ],
            [
                'code' => 'UNPAID',
                'name' => 'Nghỉ việc riêng (Không lương)',
                'annual_quota' => 0,
                'is_paid' => false,
                'deducts_balance' => false,
                'requires_attachment' => false,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ việc riêng khác không hưởng lương (phải thỏa thuận với người quản lý).',
            ],
            [
                'code' => 'MATERNITY',
                'name' => 'Nghỉ thai sản',
                'annual_quota' => 0,
                'is_paid' => true,
                'deducts_balance' => false,
                'requires_attachment' => true,
                'prorate_by_hire_date' => false,
                'description' => 'Nghỉ thai sản theo quy định của pháp luật về bảo hiểm xã hội.',
            ],
        ];

        foreach ($types as $type) {
            LeaveType::query()->create($type);
        }

        $this->command->info('Leave policy has been reset and re-seeded successfully.');
    }
}
