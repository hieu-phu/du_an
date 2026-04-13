# Database hoàn chỉnh cho dự án HRM theo nghiệp vụ ban đầu

## 1. Mục tiêu thiết kế
Thiết kế cơ sở dữ liệu phục vụ đầy đủ các nghiệp vụ ban đầu của hệ thống HRM:
- Quản lý tài khoản và phân quyền
- Quản lý nhân sự, phòng ban, chức vụ
- Quản lý địa chỉ hành chính
- Quản lý chấm công và xác nhận công
- Quản lý dự án, thành viên dự án, chi tiết triển khai
- Tính tiến độ dự án
- Duyệt thay đổi lương / phòng ban
- Thông báo hệ thống
- Phản hồi / trao đổi nội bộ
- Báo cáo, xuất file
- Nhật ký hệ thống
- Cấu hình website

---

## 2. Danh sách bảng hoàn chỉnh

## Nhóm A. Tài khoản và phân quyền
1. `users`
2. `roles`
3. `permissions`
4. `user_roles`
5. `role_permissions`
6. `login_histories`

## Nhóm B. Tổ chức nhân sự
7. `departments`
8. `positions`
9. `employee_profiles`
10. `employee_status_logs`
11. `employee_department_histories`
12. `employee_position_histories`
13. `salary_histories`

## Nhóm C. Địa chỉ hành chính
14. `provinces`
15. `districts`
16. `wards`

## Nhóm D. Chấm công
17. `attendance_records`
18. `attendance_events`
19. `attendance_approvals`
20. `attendance_monthly_summaries`
21. `work_shifts`
22. `holidays`

## Nhóm E. Dự án
23. `projects`
24. `project_roles`
25. `project_members`
26. `project_implementation_details`
27. `project_progress_histories`
28. `project_detail_logs`

## Nhóm F. Duyệt thay đổi
29. `approval_requests`
30. `approval_request_changes`

## Nhóm G. Phản hồi, trao đổi, thông báo
31. `feedback_messages`
32. `notifications`
33. `email_logs`

## Nhóm H. Báo cáo, log, cấu hình
34. `export_histories`
35. `activity_logs`
36. `site_settings`

=> Tổng cộng: **36 bảng**

---

## 3. Mô tả chức năng của từng bảng

## 3.1. Nhóm tài khoản và phân quyền

### `users`
Lưu tài khoản đăng nhập hệ thống:
- Admin
- HR
- Nhân viên

Thông tin chính:
- name
- email
- password
- google_id
- avatar
- is_active
- online_status

### `roles`
Lưu vai trò:
- admin
- hr
- employee

### `permissions`
Lưu quyền chi tiết:
- employee.create
- employee.update
- attendance.approve
- project.create
- report.export

### `user_roles`
Bảng trung gian gán vai trò cho người dùng.

### `role_permissions`
Bảng trung gian gán quyền cho vai trò.

### `login_histories`
Lưu lịch sử đăng nhập:
- user_id
- login_at
- logout_at
- ip_address
- device
- browser

---

## 3.2. Nhóm tổ chức nhân sự

### `departments`
Lưu phòng ban:
- tên phòng ban
- mô tả
- trưởng phòng
- trạng thái
- thông tin khóa

### `positions`
Lưu chức vụ:
- tên chức vụ
- mô tả
- trạng thái
- thông tin khóa

### `employee_profiles`
Lưu hồ sơ nhân sự:
- user_id
- employee_code
- department_id
- position_id
- email
- phone
- địa chỉ
- ngày sinh
- ngày vào làm
- lương cơ bản
- loại tiền tệ
- trạng thái làm việc
- người quản lý trực tiếp

### `employee_status_logs`
Lưu lịch sử thay đổi trạng thái nhân sự:
- active
- probation
- on_leave
- terminated
- locked

### `employee_department_histories`
Lưu lịch sử điều chuyển phòng ban:
- employee_profile_id
- old_department_id
- new_department_id
- changed_at
- changed_by

### `employee_position_histories`
Lưu lịch sử thay đổi chức vụ:
- employee_profile_id
- old_position_id
- new_position_id
- changed_at
- changed_by

### `salary_histories`
Lưu lịch sử thay đổi lương chính thức sau duyệt:
- employee_profile_id
- old_salary
- new_salary
- effective_date
- approved_by
- note

---

## 3.3. Nhóm địa chỉ hành chính

### `provinces`
Lưu tỉnh / thành phố.

### `districts`
Lưu quận / huyện.

### `wards`
Lưu xã / phường.

Mục đích:
- phục vụ chọn địa chỉ theo cấp hành chính
- chuẩn hóa dữ liệu địa chỉ

---

## 3.4. Nhóm chấm công

### `attendance_records`
Lưu bản ghi chấm công theo ngày:
- employee_profile_id
- work_date
- check_in_at
- check_out_at
- worked_minutes
- attendance_status
- confirmed_by
- confirmed_at

### `attendance_events`
Lưu từng sự kiện chấm công:
- check_in
- check_out
- manual_adjustment

### `attendance_approvals`
Lưu lịch sử HR duyệt công:
- attendance_record_id
- approval_type
- approved_by
- approved_at
- status
- note

### `attendance_monthly_summaries`
Lưu tổng hợp công theo tháng:
- employee_profile_id
- month
- year
- total_working_days
- total_present_days
- total_absent_days
- total_late_count
- total_early_leave_count

### `work_shifts`
Lưu ca làm việc chuẩn:
- shift_name
- start_time
- end_time
- standard_minutes
- grace_minutes

### `holidays`
Lưu ngày nghỉ lễ:
- holiday_date
- holiday_name
- is_paid_leave

---

## 3.5. Nhóm dự án

### `projects`
Lưu dự án:
- name
- start_date
- status
- description
- locked info
- created_by
- updated_by

### `project_roles`
Lưu vai trò trong từng dự án:
- Leader
- Backend
- Frontend
- Tester
- BA

### `project_members`
Lưu thành viên tham gia dự án:
- project_id
- employee_profile_id
- project_role_id
- joined_at
- left_at
- is_active

### `project_implementation_details`
Lưu chi tiết triển khai:
- project_id
- assigned_to
- content
- execution_date
- duration_days
- expected_end_date
- detail_status

### `project_progress_histories`
Lưu lịch sử thay đổi tiến độ dự án:
- project_id
- old_progress
- new_progress
- changed_at
- changed_by
- note

### `project_detail_logs`
Lưu lịch sử cập nhật chi tiết triển khai:
- implementation_detail_id
- field_name
- old_value
- new_value
- updated_by
- updated_at

---

## 3.6. Nhóm duyệt thay đổi

### `approval_requests`
Lưu yêu cầu chờ duyệt:
- request_type
- target_type
- target_id
- requested_by
- reviewed_by
- status
- submitted_at
- reviewed_at
- reason
- review_note

Dùng cho:
- đổi lương cơ bản
- thêm / sửa / khóa phòng ban
- các thay đổi cần Admin duyệt

### `approval_request_changes`
Lưu chi tiết trường thay đổi:
- approval_request_id
- field_name
- old_value
- new_value

---

## 3.7. Nhóm phản hồi, trao đổi, thông báo

### `feedback_messages`
Lưu phản hồi / đề xuất nội bộ:
- sender_id
- receiver_id
- receiver_group
- subject
- message
- status
- read_at

### `notifications`
Lưu thông báo hệ thống:
- user_id
- title
- message
- category
- reference_type
- reference_id
- is_read
- read_at
- data

Dùng cho:
- HR nhận thông báo check-in / check-out
- thông báo duyệt yêu cầu
- thông báo nội bộ

### `email_logs`
Lưu log gửi mail:
- sender_id
- receiver_email
- subject
- body_summary
- sent_at
- status
- error_message

---

## 3.8. Nhóm báo cáo, log, cấu hình

### `export_histories`
Lưu lịch sử xuất báo cáo:
- user_id
- module
- file_type
- filter_data
- file_path
- exported_at

### `activity_logs`
Lưu log thao tác hệ thống:
- user_id
- module
- action
- description
- reference_table
- reference_id
- ip_address
- created_at

### `site_settings`
Lưu cấu hình website:
- site_name
- logo_path
- favicon_path
- header_content
- footer_content
- contact_email
- contact_phone
- updated_by

---

## 4. Quan hệ chính giữa các bảng

### Tài khoản và phân quyền
- users 1-n user_roles
- roles 1-n user_roles
- roles 1-n role_permissions
- permissions 1-n role_permissions

### Nhân sự
- users 1-1 employee_profiles
- departments 1-n employee_profiles
- positions 1-n employee_profiles
- employee_profiles 1-n employee_status_logs
- employee_profiles 1-n employee_department_histories
- employee_profiles 1-n employee_position_histories
- employee_profiles 1-n salary_histories

### Địa chỉ
- provinces 1-n districts
- provinces 1-n wards
- districts 1-n wards
- provinces / districts / wards 1-n employee_profiles

### Chấm công
- employee_profiles 1-n attendance_records
- attendance_records 1-n attendance_events
- attendance_records 1-n attendance_approvals
- employee_profiles 1-n attendance_monthly_summaries
- work_shifts có thể gắn với employee_profiles nếu mở rộng phân ca

### Dự án
- projects 1-n project_roles
- projects 1-n project_members
- employee_profiles 1-n project_members
- project_roles 1-n project_members
- projects 1-n project_implementation_details
- employee_profiles 1-n project_implementation_details
- projects 1-n project_progress_histories
- project_implementation_details 1-n project_detail_logs

### Duyệt thay đổi
- approval_requests 1-n approval_request_changes
- users 1-n approval_requests (requested_by)
- users 1-n approval_requests (reviewed_by)

### Phản hồi và thông báo
- users 1-n notifications
- users 1-n feedback_messages (sender / receiver)
- users 1-n email_logs

### Báo cáo và log
- users 1-n export_histories
- users 1-n activity_logs
- users 1-n site_settings (updated_by)

---

## 5. Những bảng bắt buộc phải làm trước

Nếu muốn triển khai đúng nghiệp vụ mà vẫn tối ưu tiến độ, nên ưu tiên theo thứ tự sau:

### Giai đoạn 1: nền tảng
- users
- roles
- permissions
- user_roles
- role_permissions
- login_histories

### Giai đoạn 2: nhân sự
- departments
- positions
- employee_profiles
- provinces
- districts
- wards

### Giai đoạn 3: chấm công
- attendance_records
- attendance_events
- attendance_approvals
- attendance_monthly_summaries

### Giai đoạn 4: dự án
- projects
- project_roles
- project_members
- project_implementation_details
- project_progress_histories

### Giai đoạn 5: duyệt và thông báo
- approval_requests
- approval_request_changes
- notifications
- feedback_messages

### Giai đoạn 6: hỗ trợ mở rộng
- salary_histories
- employee_department_histories
- employee_position_histories
- project_detail_logs
- email_logs
- export_histories
- activity_logs
- site_settings
- work_shifts
- holidays

---

## 6. Bộ bảng tối thiểu nhưng vẫn đủ nghiệp vụ
Nếu muốn làm nhanh bản đầu tiên mà vẫn không lệch nghiệp vụ, tối thiểu nên có:

1. users
2. roles
3. permissions
4. user_roles
5. role_permissions
6. departments
7. positions
8. employee_profiles
9. provinces
10. districts
11. wards
12. attendance_records
13. attendance_events
14. attendance_approvals
15. attendance_monthly_summaries
16. projects
17. project_roles
18. project_members
19. project_implementation_details
20. project_progress_histories
21. approval_requests
22. approval_request_changes
23. feedback_messages
24. notifications
25. site_settings
26. activity_logs
27. export_histories

=> Bộ này là **core đầy đủ** để bám sát nghiệp vụ ban đầu.

---

## 7. Kết luận
Theo nghiệp vụ ban đầu bạn đưa ra, bản database hoàn chỉnh nên có **36 bảng** nếu làm đủ chiều sâu quản lý và lịch sử thay đổi.

Nếu cần cân bằng giữa tốc độ triển khai và độ đầy đủ nghiệp vụ, bạn có thể dùng bộ **27 bảng core** trước, sau đó mở rộng thêm các bảng lịch sử và hỗ trợ nâng cao.

Đây là bản chốt phù hợp để:
- vẽ ERD
- viết migration
- chia module code
- làm tài liệu phân tích nghiệp vụ
- triển khai hệ thống theo đúng yêu cầu công ty
