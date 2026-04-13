CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    module VARCHAR(100) NOT NULL
);

CREATE TABLE user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id),
    CONSTRAINT uk_user_roles UNIQUE (user_id, role_id)
);

CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id),
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id),
    CONSTRAINT uk_role_permissions UNIQUE (role_id, permission_id)
);

CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
);

CREATE TABLE positions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    position_code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
);

CREATE TABLE provinces (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE wards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    province_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    CONSTRAINT fk_wards_province FOREIGN KEY (province_id) REFERENCES provinces(id)
);

CREATE TABLE employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    employee_code VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    province_id BIGINT UNSIGNED NULL,
    ward_id BIGINT UNSIGNED NULL,
    address_detail VARCHAR(500) NULL,
    date_of_birth DATE NULL,
    join_date DATE NOT NULL,
    basic_salary DECIMAL(15,2) NOT NULL DEFAULT 0,
    department_id BIGINT UNSIGNED NULL,
    position_id BIGINT UNSIGNED NULL,
    employment_status ENUM('active', 'inactive', 'probation', 'on_leave', 'terminated') NOT NULL DEFAULT 'active',
    is_online TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_employees_province FOREIGN KEY (province_id) REFERENCES provinces(id),
    CONSTRAINT fk_employees_ward FOREIGN KEY (ward_id) REFERENCES wards(id),
    CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_employees_position FOREIGN KEY (position_id) REFERENCES positions(id)
);

CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    status ENUM('planning', 'in_progress', 'on_hold', 'completed') NOT NULL DEFAULT 'in_progress',
    description TEXT NULL,
    progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_projects_created_by FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT fk_projects_updated_by FOREIGN KEY (updated_by) REFERENCES users(id)
);

CREATE TABLE project_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    CONSTRAINT fk_project_roles_project FOREIGN KEY (project_id) REFERENCES projects(id),
    CONSTRAINT uk_project_roles UNIQUE (project_id, name)
);

CREATE TABLE project_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    employee_id BIGINT UNSIGNED NOT NULL,
    project_role_id BIGINT UNSIGNED NULL,
    joined_at DATE NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    CONSTRAINT fk_project_members_project FOREIGN KEY (project_id) REFERENCES projects(id),
    CONSTRAINT fk_project_members_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT fk_project_members_project_role FOREIGN KEY (project_role_id) REFERENCES project_roles(id),
    CONSTRAINT uk_project_members UNIQUE (project_id, employee_id)
);

CREATE TABLE project_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    employee_id BIGINT UNSIGNED NULL,
    execute_date DATE NOT NULL,
    duration_days INT NOT NULL,
    expected_finish_date DATE NOT NULL,
    weight_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    status ENUM('planned', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'planned',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_project_tasks_project FOREIGN KEY (project_id) REFERENCES projects(id),
    CONSTRAINT fk_project_tasks_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT fk_project_tasks_created_by FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT fk_project_tasks_updated_by FOREIGN KEY (updated_by) REFERENCES users(id)
);

CREATE TABLE project_progress_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    old_progress DECIMAL(5,2) NOT NULL DEFAULT 0,
    new_progress DECIMAL(5,2) NOT NULL DEFAULT 0,
    note TEXT NULL,
    changed_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_project_progress_histories_project FOREIGN KEY (project_id) REFERENCES projects(id),
    CONSTRAINT fk_project_progress_histories_changed_by FOREIGN KEY (changed_by) REFERENCES users(id)
);

CREATE TABLE attendance_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    check_in_time DATETIME NULL,
    check_out_time DATETIME NULL,
    check_in_status ENUM('on_time', 'late', 'absent', 'leave') NOT NULL DEFAULT 'on_time',
    working_minutes INT NOT NULL DEFAULT 0,
    working_days DECIMAL(4,2) NOT NULL DEFAULT 0,
    is_approved_by BIGINT UNSIGNED NULL,
    is_approved_at DATETIME NULL,
    CONSTRAINT fk_attendance_logs_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT fk_attendance_logs_approved_by FOREIGN KEY (is_approved_by) REFERENCES users(id),
    CONSTRAINT uk_attendance_logs UNIQUE (employee_id, attendance_date)
);

CREATE TABLE attendance_approvals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attendance_log_id BIGINT UNSIGNED NOT NULL,
    action_type ENUM('check_in', 'check_out', 'manual_update') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    note TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at DATETIME NULL,
    CONSTRAINT fk_attendance_approvals_log FOREIGN KEY (attendance_log_id) REFERENCES attendance_logs(id),
    CONSTRAINT fk_attendance_approvals_approved_by FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE attendance_monthly_summaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    total_working_days DECIMAL(5,2) NOT NULL DEFAULT 0,
    total_present_days DECIMAL(5,2) NOT NULL DEFAULT 0,
    total_absent_days DECIMAL(5,2) NOT NULL DEFAULT 0,
    total_late_count INT NOT NULL DEFAULT 0,
    total_early_leave_count INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_attendance_monthly_summaries_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT uk_attendance_monthly_summaries UNIQUE (employee_id, month, year)
);

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    category VARCHAR(100) NULL,
    reference_type VARCHAR(100) NULL,
    reference_id BIGINT UNSIGNED NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    data JSON NULL,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE approval_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_type ENUM('salary_update', 'department_update', 'position_update', 'other') NOT NULL,
    reference_table VARCHAR(100) NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    note TEXT NULL,
    approved_note TEXT NULL,
    approved_at DATETIME NULL,
    CONSTRAINT fk_approval_requests_requested_by FOREIGN KEY (requested_by) REFERENCES users(id),
    CONSTRAINT fk_approval_requests_approved_by FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE approval_request_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    approval_request_id BIGINT UNSIGNED NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    CONSTRAINT fk_approval_request_items_request FOREIGN KEY (approval_request_id) REFERENCES approval_requests(id)
);

CREATE TABLE feedbacks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    reply_type ENUM('admin', 'hr') NOT NULL,
    status ENUM('pending', 'replied', 'closed') NOT NULL DEFAULT 'pending',
    replied_by BIGINT UNSIGNED NULL,
    replied_at DATETIME NULL,
    reply_content TEXT NULL,
    CONSTRAINT fk_feedbacks_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT fk_feedbacks_replied_by FOREIGN KEY (replied_by) REFERENCES users(id)
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    module VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    reference_table VARCHAR(100) NULL,
    reference_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE export_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    module VARCHAR(100) NOT NULL,
    file_type ENUM('excel', 'pdf') NOT NULL,
    file_data TEXT NULL,
    file_path VARCHAR(500) NULL,
    CONSTRAINT fk_export_histories_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE site_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255) NOT NULL,
    logo VARCHAR(500) NULL,
    favicon VARCHAR(500) NULL,
    header_html TEXT NULL,
    footer_html TEXT NULL,
    updated_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_site_settings_updated_by FOREIGN KEY (updated_by) REFERENCES users(id)
);
