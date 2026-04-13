# HRM Base From Mermaid Diagram

Toi da tao mot ban SQL bam sat theo schema trong anh:

- File SQL: `database/schema/hrm_mermaid_base.sql`

## Cac bang trong base nay

- `users`
- `roles`
- `permissions`
- `user_roles`
- `role_permissions`
- `departments`
- `positions`
- `provinces`
- `wards`
- `employees`
- `projects`
- `project_roles`
- `project_members`
- `project_tasks`
- `project_progress_histories`
- `attendance_logs`
- `attendance_approvals`
- `attendance_monthly_summaries`
- `notifications`
- `approval_requests`
- `approval_request_items`
- `feedbacks`
- `activity_logs`
- `export_histories`
- `site_settings`

## Luu y

- SQL nay duoc tao theo naming trong anh, nen se khac voi migration Laravel toi da tao truoc do.
- Mot vai truong trong anh khong doc duoc 100 phan tram do do phan giai, toi da suy luan theo nghiep vu gan nhat de co schema chay duoc.
- Neu anh/chị muon, toi co the lam tiep 1 trong 2 huong:

1. Chuyen file SQL nay thanh migration Laravel dung ten bang/y nhu anh.
2. Dong bo schema hien tai cua du an Laravel sang dung theo base trong anh.
