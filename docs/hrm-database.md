# HRM Database Design

## Pham vi

Schema nay duoc thiet ke theo tai lieu "MO TA CHUC NANG APP WEBSITE HRM", bao gom:

- Quan ly nhan su, phong ban, chuc vu
- Cham cong va xac nhan ngay cong
- Quan ly du an va chi tiet trien khai
- Phan quyen Admin, HR, Nhan vien
- Phe duyet thay doi luong/phong ban
- Cau hinh header, footer, logo, favicon

## Bang chinh

- `users`: tai khoan dang nhap, email, avatar, session, role/permission
- `employee_profiles`: ho so nhan su, phong ban, chuc vu, dia chi, ngay sinh, ngay vao lam, luong co ban
- `departments`: phong ban
- `positions`: chuc vu
- `attendance_records`: mot ban ghi cham cong cho moi nhan su trong moi ngay
- `attendance_events`: log check-in/check-out va dieu chinh cong
- `projects`: thong tin du an
- `project_roles`: vai tro rieng trong tung du an
- `project_members`: danh sach nhan su tham gia du an
- `project_implementation_details`: cac dau viec/trien khai trong du an
- `approval_requests`: yeu cau cho admin phe duyet
- `approval_request_changes`: chi tiet cac truong thay doi
- `feedback_messages`: nhan vien gui phan hoi/de xuat cho HR/Admin
- `site_settings`: tuy bien header/footer/logo/favicon

## Quan he chinh

- `users` 1-1 `employee_profiles`
- `departments` 1-n `employee_profiles`
- `positions` 1-n `employee_profiles`
- `employee_profiles` 1-n `attendance_records`
- `attendance_records` 1-n `attendance_events`
- `projects` 1-n `project_roles`
- `projects` 1-n `project_members`
- `projects` 1-n `project_implementation_details`
- `employee_profiles` 1-n `project_members`
- `approval_requests` 1-n `approval_request_changes`

## Tinh ty le hoan thanh du an

Nen tinh dong theo cong thuc:

`SUM(duration_days cua detail_status = 'completed') / SUM(duration_days tat ca detail khong bi cancelled) * 100`

Neu du an chua co `project_implementation_details` thi ty le mac dinh bang `0`.

## Ghi chu trien khai

- Danh sach nhan su co the loc theo ten trong `users.name`, theo ngay vao lam trong `employee_profiles.hire_date`, theo phong ban trong `employee_profiles.department_id`
- Avatar Gmail co the lay tu `social_accounts.avatar` hoac `users.avatar`
- Trang thai online/offline co the suy ra tu `sessions` va `users.last_login_at`
- Xuat Excel/PDF la chuc nang ung dung, khong can bang rieng
- Bao cao thang co the tong hop truc tiep tu `attendance_records`
- Luong co ban dang public duoc luu tai `employee_profiles.base_salary`, thay doi cho duyet duoc luu tai `approval_requests`
