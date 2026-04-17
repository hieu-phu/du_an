-- =========================================================
-- HRM Capability Catalog Seed
-- File: hrm_position_capabilities_seed.sql
-- Purpose:
--   Seed hoàn chỉnh và chuẩn hóa danh mục quyền hệ thống HRM
--   Phân loại đúng theo giao diện, không dư thừa, đầy đủ chức năng
--
-- Notes:
--   1) Sử dụng INSERT ... ON DUPLICATE KEY UPDATE
--   2) Cột `code` là UNIQUE, chạy lệnh này trước nếu chưa có:
--        ALTER TABLE position_capabilities
--        ADD UNIQUE KEY uk_position_capabilities_code (code);
-- =========================================================

START TRANSACTION;

INSERT INTO position_capabilities
    (code, name, module, description, is_system, is_active, created_at, updated_at)
VALUES

-- ✅ NHÂN SỰ
('manage_employees',            'Quản lý nhân sự',                  'human_resources', 'Thêm, sửa, khóa nhân sự', 1, 1, NOW(), NOW()),
('view_all_profiles',           'Xem lương nhân viên',              'human_resources', 'Xem mức lương của toàn bộ nhân sự', 1, 1, NOW(), NOW()),
('view_own_profile',            'Xem hồ sơ cá nhân',                'human_resources', 'Xem thông tin hồ sơ của chính mình', 1, 1, NOW(), NOW()),
('update_own_profile',          'Cập nhật hồ sơ cá nhân',           'human_resources', 'Cập nhật thông tin hồ sơ cá nhân', 1, 1, NOW(), NOW()),
('create_employee',             'Tạo nhân sự',                      'human_resources', 'Tạo mới hồ sơ nhân sự', 1, 1, NOW(), NOW()),
('lock_employee',               'Khóa nhân sự',                     'human_resources', 'Khóa tài khoản hoặc trạng thái nhân sự', 1, 1, NOW(), NOW()),
('transfer_employee',           'Điều chuyển nhân sự',              'human_resources', 'Chuyển nhân viên sang phòng ban khác', 1, 1, NOW(), NOW()),
('request_update_employee',     'Yêu cầu sửa nhân sự',              'human_resources', 'Tạo yêu cầu thay đổi thông tin nhân sự', 1, 1, NOW(), NOW()),
('approve_update_employee',     'Duyệt sửa nhân sự',                'human_resources', 'Phê duyệt yêu cầu thay đổi thông tin nhân sự', 1, 1, NOW(), NOW()),

-- ✅ LƯƠNG
('manage_salary',               'Điều chỉnh lương',                 'salary',          'Đề xuất và phê duyệt thay đổi lương', 1, 1, NOW(), NOW()),
('view_own_salary',             'Xem lương cá nhân',                'salary',          'Xem mức lương của chính mình', 1, 1, NOW(), NOW()),
('view_all_salary',             'Xem lương toàn công ty',           'salary',          'Xem mức lương của toàn bộ nhân sự', 1, 1, NOW(), NOW()),
('view_salary_history',         'Xem lịch sử lương',                'salary',          'Xem lịch sử thay đổi lương', 1, 1, NOW(), NOW()),
('request_salary_change',       'Yêu cầu đổi lương',                'salary',          'Tạo đề xuất thay đổi lương', 1, 1, NOW(), NOW()),
('approve_salary_change',       'Duyệt đổi lương',                  'salary',          'Phê duyệt đề xuất thay đổi lương', 1, 1, NOW(), NOW()),
('export_salary',               'Xuất dữ liệu lương',               'salary',          'Xuất báo cáo hoặc dữ liệu lương', 1, 1, NOW(), NOW()),
('generate_payroll',            'Tạo bảng lương',                   'salary',          'Sinh bảng lương theo kỳ lương', 1, 1, NOW(), NOW()),

-- ✅ CHẤM CÔNG
('approve_attendance',          'Duyệt chấm công',                  'attendance',      'Xác nhận và phê duyệt công của nhân viên', 1, 1, NOW(), NOW()),
('view_all_attendance',         'Xem toàn bộ chấm công',            'attendance',      'Xem báo cáo công của tất cả nhân sự', 1, 1, NOW(), NOW()),
('export_attendance',           'Xuất báo cáo chấm công',           'attendance',      'Xuất Excel / PDF chấm công', 1, 1, NOW(), NOW()),
('check_in',                    'Chấm công vào',                    'attendance',      'Thực hiện check-in', 1, 1, NOW(), NOW()),
('check_out',                   'Chấm công ra',                     'attendance',      'Thực hiện check-out', 1, 1, NOW(), NOW()),
('view_own_attendance',         'Xem chấm công cá nhân',            'attendance',      'Xem dữ liệu chấm công của chính mình', 1, 1, NOW(), NOW()),
('view_team_attendance',        'Xem chấm công team',               'attendance',      'Xem dữ liệu chấm công trong phạm vi team', 1, 1, NOW(), NOW()),
('view_department_attendance',  'Xem chấm công phòng ban',          'attendance',      'Xem dữ liệu chấm công trong phạm vi phòng ban', 1, 1, NOW(), NOW()),
('request_attendance_adjustment','Yêu cầu chỉnh công',              'attendance',      'Tạo yêu cầu chỉnh công', 1, 1, NOW(), NOW()),
('approve_team_attendance',     'Duyệt chấm công team',             'attendance',      'Phê duyệt công trong phạm vi team', 1, 1, NOW(), NOW()),
('approve_department_attendance','Duyệt chấm công phòng ban',       'attendance',      'Phê duyệt công trong phạm vi phòng ban', 1, 1, NOW(), NOW()),
('lock_attendance_month',       'Khóa tháng công',                  'attendance',      'Khóa dữ liệu công theo tháng', 1, 1, NOW(), NOW()),
('unlock_attendance_month',     'Mở khóa tháng công',               'attendance',      'Mở khóa dữ liệu công theo tháng', 1, 1, NOW(), NOW()),

-- ✅ NGHỈ PHÉP
('approve_leave',               'Phê duyệt ngày nghỉ',              'leave',           'Duyệt hoặc từ chối đơn xin nghỉ', 1, 1, NOW(), NOW()),
('manage_leave_policy',         'Quản lý chính sách nghỉ',          'leave',           'Thiết lập quy định nghỉ phép', 1, 1, NOW(), NOW()),
('request_leave',               'Tạo đơn nghỉ phép',                'leave',           'Gửi yêu cầu nghỉ phép', 1, 1, NOW(), NOW()),
('view_own_leave_requests',     'Xem đơn nghỉ cá nhân',             'leave',           'Xem các đơn nghỉ của chính mình', 1, 1, NOW(), NOW()),
('view_team_leave_requests',    'Xem đơn nghỉ team',                'leave',           'Xem các đơn nghỉ trong phạm vi team', 1, 1, NOW(), NOW()),
('view_department_leave_requests','Xem đơn nghỉ phòng ban',         'leave',           'Xem các đơn nghỉ trong phạm vi phòng ban', 1, 1, NOW(), NOW()),
('view_all_leave_requests',     'Xem toàn bộ đơn nghỉ',             'leave',           'Xem tất cả đơn nghỉ trong hệ thống', 1, 1, NOW(), NOW()),
('approve_team_leave',          'Duyệt nghỉ team',                  'leave',           'Duyệt đơn nghỉ trong phạm vi team', 1, 1, NOW(), NOW()),
('approve_department_leave',    'Duyệt nghỉ phòng ban',             'leave',           'Duyệt đơn nghỉ trong phạm vi phòng ban', 1, 1, NOW(), NOW()),

-- ✅ TĂNG CA
('request_overtime',            'Tạo đơn tăng ca',                  'overtime',        'Gửi yêu cầu tăng ca', 1, 1, NOW(), NOW()),
('view_own_overtime',           'Xem OT cá nhân',                   'overtime',        'Xem dữ liệu tăng ca của chính mình', 1, 1, NOW(), NOW()),
('view_team_overtime',          'Xem OT team',                      'overtime',        'Xem dữ liệu tăng ca trong phạm vi team', 1, 1, NOW(), NOW()),
('view_department_overtime',    'Xem OT phòng ban',                 'overtime',        'Xem dữ liệu tăng ca trong phạm vi phòng ban', 1, 1, NOW(), NOW()),
('view_all_overtime',           'Xem toàn bộ OT',                   'overtime',        'Xem toàn bộ dữ liệu tăng ca', 1, 1, NOW(), NOW()),
('approve_overtime',            'Duyệt tăng ca',                    'overtime',        'Phê duyệt đơn tăng ca', 1, 1, NOW(), NOW()),
('approve_team_overtime',       'Duyệt OT team',                    'overtime',        'Duyệt đơn tăng ca trong phạm vi team', 1, 1, NOW(), NOW()),
('approve_department_overtime', 'Duyệt OT phòng ban',               'overtime',        'Duyệt đơn tăng ca trong phạm vi phòng ban', 1, 1, NOW(), NOW()),

-- ✅ DỰ ÁN
('manage_projects',             'Quản lý dự án',                    'project',         'Tạo, sửa, phân công dự án', 1, 1, NOW(), NOW()),
('manage_project_members',      'Quản lý thành viên dự án',         'project',         'Thêm/xóa thành viên dự án', 1, 1, NOW(), NOW()),
('manage_project_roles',        'Quản lý vai trò dự án',            'project',         'Tạo và xóa vai trò trong từng dự án', 1, 1, NOW(), NOW()),
('view_all_projects',           'Xem toàn bộ dự án',                'project',         'Xem tất cả dự án trong hệ thống', 1, 1, NOW(), NOW()),
('view_own_projects',           'Xem dự án cá nhân',                'project',         'Xem các dự án mình tham gia', 1, 1, NOW(), NOW()),
('view_team_projects',          'Xem dự án team',                   'project',         'Xem dự án trong phạm vi team', 1, 1, NOW(), NOW()),
('view_department_projects',    'Xem dự án phòng ban',              'project',         'Xem dự án trong phạm vi phòng ban', 1, 1, NOW(), NOW()),
('create_project',              'Tạo dự án',                        'project',         'Tạo mới dự án', 1, 1, NOW(), NOW()),
('update_project',              'Cập nhật dự án',                   'project',         'Cập nhật thông tin dự án', 1, 1, NOW(), NOW()),
('delete_project',              'Xóa dự án',                        'project',         'Xóa hoặc hủy dự án', 1, 1, NOW(), NOW()),
('request_project_change',      'Yêu cầu sửa dự án',                'project',         'Tạo yêu cầu thay đổi dự án', 1, 1, NOW(), NOW()),
('approve_project_change',      'Duyệt sửa dự án',                  'project',         'Phê duyệt thay đổi dự án', 1, 1, NOW(), NOW()),
('assign_project_member',       'Phân công thành viên dự án',       'project',         'Gán nhân sự vào dự án', 1, 1, NOW(), NOW()),
('remove_project_member',       'Xóa thành viên dự án',             'project',         'Gỡ nhân sự khỏi dự án', 1, 1, NOW(), NOW()),
('update_project_task_status',  'Cập nhật trạng thái đầu việc',     'project',         'Cập nhật trạng thái task', 1, 1, NOW(), NOW()),
('lock_project',                'Khóa dự án',                       'project',         'Khóa chỉnh sửa dự án', 1, 1, NOW(), NOW()),

-- ✅ PHÒNG BAN & TỔ CHỨC
('manage_departments',          'Quản lý phòng ban',                'organization',    'Thêm, sửa, khóa phòng ban', 1, 1, NOW(), NOW()),
('manage_positions',            'Quản lý chức vụ',                  'organization',    'Thêm, sửa, khóa chức vụ', 1, 1, NOW(), NOW()),
('view_departments',            'Xem phòng ban',                    'organization',    'Xem danh sách phòng ban', 1, 1, NOW(), NOW()),
('view_positions',              'Xem chức vụ',                      'organization',    'Xem danh sách chức vụ', 1, 1, NOW(), NOW()),
('request_department_change',   'Yêu cầu sửa phòng ban',            'organization',    'Tạo yêu cầu thay đổi phòng ban', 1, 1, NOW(), NOW()),
('approve_department_change',   'Duyệt sửa phòng ban',              'organization',    'Phê duyệt thay đổi phòng ban', 1, 1, NOW(), NOW()),
('request_position_change',     'Yêu cầu sửa chức vụ',              'organization',    'Tạo yêu cầu thay đổi chức vụ', 1, 1, NOW(), NOW()),
('approve_position_change',     'Duyệt sửa chức vụ',                'organization',    'Phê duyệt thay đổi chức vụ', 1, 1, NOW(), NOW()),
('view_work_shifts',            'Xem ca làm việc',                  'organization',    'Xem danh sách ca làm việc', 1, 1, NOW(), NOW()),
('manage_work_shifts',          'Quản lý ca làm việc',              'organization',    'Thêm, sửa, khóa ca làm việc', 1, 1, NOW(), NOW()),
('view_holidays',               'Xem ngày nghỉ lễ',                 'organization',    'Xem danh sách ngày nghỉ lễ', 1, 1, NOW(), NOW()),
('manage_holidays',             'Quản lý ngày nghỉ lễ',             'organization',    'Thêm, sửa, xóa ngày nghỉ lễ', 1, 1, NOW(), NOW()),

-- ✅ DUYỆT & KÝ KẾT
('approve_requests',            'Duyệt yêu cầu chung',              'approval',        'Phê duyệt các yêu cầu từ nhân viên', 1, 1, NOW(), NOW()),
('sign_documents',              'Ký duyệt văn bản',                 'approval',        'Ký và phê duyệt hợp đồng, quyết định', 1, 1, NOW(), NOW()),
('cancel_requests',             'Hủy yêu cầu',                      'approval',        'Hủy yêu cầu đang chờ xử lý khi được phép', 1, 1, NOW(), NOW()),
('view_approval_requests',      'Xem yêu cầu duyệt',                'approval',        'Xem danh sách các yêu cầu cần duyệt', 1, 1, NOW(), NOW()),

-- ✅ BÁO CÁO & HỆ THỐNG
('view_reports',                'Xem báo cáo tổng hợp',             'report',          'Dashboard và báo cáo toàn hệ thống', 1, 1, NOW(), NOW()),
('export_reports',              'Xuất báo cáo',                     'report',          'Xuất dữ liệu Excel / PDF', 1, 1, NOW(), NOW()),
('view_financial_reports',      'Xem báo cáo tài chính',            'report',          'Xem báo cáo tài chính hoặc báo cáo chi phí', 1, 1, NOW(), NOW()),
('view_dashboard',              'Xem dashboard tổng quan',          'system',          'Xem dashboard và thông tin tổng quan', 1, 1, NOW(), NOW()),
('manage_settings',             'Quản lý cấu hình hệ thống',        'system',          'Xem và cập nhật cấu hình hệ thống', 1, 1, NOW(), NOW()),
('view_settings',               'Xem cấu hình hệ thống',            'system',          'Xem cấu hình hệ thống', 1, 1, NOW(), NOW()),
('manage_users',                'Quản lý tài khoản',                'system',          'Tạo, sửa, khóa tài khoản người dùng', 1, 1, NOW(), NOW()),
('view_activity_logs',          'Xem nhật ký hoạt động',            'system',          'Xem lịch sử thao tác hệ thống', 1, 1, NOW(), NOW()),

-- ✅ PHẢN HỒI
('view_feedbacks',              'Xem phản hồi',                     'feedback',        'Xem danh sách feedback nội bộ', 1, 1, NOW(), NOW()),
('create_feedback',             'Tạo phản hồi',                     'feedback',        'Gửi feedback nội bộ', 1, 1, NOW(), NOW()),
('reply_feedback',              'Phản hồi feedback',                'feedback',        'Trả lời feedback nội bộ', 1, 1, NOW(), NOW()),
('manage_feedbacks',            'Quản lý phản hồi',                 'feedback',        'Quản lý, xử lý feedback nội bộ', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    module = VALUES(module),
    description = VALUES(description),
    is_system = VALUES(is_system),
    is_active = VALUES(is_active),
    updated_at = VALUES(updated_at);

COMMIT;
