# 📚 BaseAsfy System Architecture

BaseAsfy là core framework cho các dự án phát triển tại Asfy Tech.
Hệ thống sử dụng Laravel 12 làm Backend và Vue 3 (InertiaJS) làm Frontend.

## 1. Cấu trúc Backend (Laravel)

Sử dụng chuẩn kiến trúc MVC kết hợp với Service + Repository Pattern.

### Flow xử lý Request (Backend):

`Route -> Middleware (Auth/Role) -> Controller -> Service (Business Logic) -> Repository (Data Access) -> Model -> Database`

- **Controllers** (`app/Http/Controllers/`): Hạn chế ghi Business logic. Các controller kế thừa từ `Controller.php` đã được đóng gói sẵn các hàm trả về chuẩn như `sendSuccess`, `sendError`.
- **Services** (`app/Services/`): Đảm nhận xử lý các Business Logic và db Transactions thông qua DB helpers (kế thừa `BaseService.php`). **KHÔNG** chứa Eloquent query trực tiếp — uỷ quyền truy vấn DB cho Repository.
- **Repositories** (`app/Repositories/`): Đảm nhận giao tiếp với Database thông qua Eloquent Model. Kế thừa `BaseRepository.php` (CRUD chung) và implement `RepositoryInterface`. Mỗi module một Repository riêng.
- **Requests** (`app/Http/Requests/`): Tách biệt logic Role checking / Form Validation.
- **Resources** (`app/Http/Resources/`): Định dạng chuẩn đầu ra JSON API.
- **DTOs** (`app/DTOs/`): Data Transfer Objects giúp bind chặt kiểu dữ liệu từ Controller xuống Service thay vì dùng array truyền thống.
- **Traits** (`app/Models/Traits/`): Các global scope phổ biến để dùng lại (`HasStatusScope`, `Filterable`).

## 2. Cấu trúc Frontend (Vue 3 + InertiaJS)

Frontend là một Single Page Application (SPA) với Vite build system & SSR optimize support.

### Các thành phần chính:

- **Pages** (`resources/js/Pages/`): Component đóng vai trò đại diện cho một màn hình độc lập (được gọi từ Inertia `Inertia::render()`).
- **Components** (`resources/js/components/`): Chứa các Reusable UI (Buttons, Tables, Forms...) độc lập và có thể plug-and-play.
- **Layouts** (`resources/js/Layouts/`): Bao gồm Layout dùng chung (Admin, Auth, Public).
- **Utils** (`resources/js/utils/`): `api.ts` quản lý Axios Config, `formatters.ts` cho các hàm format chung (tiền tệ, ngày tháng...).

## 3. Quy chuẩn chung Code & Tái sử dụng

- **Component UI**: Các UI component chung nằm tại `resources/js/components/ui/`. Khi clone base, tuyệt đối không sửa đổi logic phần base component trừ khi có update logic từ core.
- **State Management**: Sử dụng Vuex4 được chia các module tương ứng. Hạn chế lạm dụng state toàn cục cho những logic form nội bộ.

## 4. Quy tắc Đặt tên (Naming Conventions)

Tuân thủ nghiêm ngặt **Clean Code**: tên phải thể hiện rõ **ý định (Intent-revealing)**, không viết tắt khó hiểu.

### 4.1. Quy tắc Cốt lõi
- **Biến (Variables):** `camelCase` (VD: `userCount`, `activeUsers`). Không dùng `n`, `u`.
- **Hàm (Functions):** `camelCase`, bắt đầu bằng Động từ + Danh từ (VD: `getUserById()`).
- **Boolean:** Tiền tố `is`, `has`, `can`, `should` (VD: `isActive`, `hasPermission`).
- **Hằng số (Constants):** `SCREAMING_SNAKE_CASE` (VD: `MAX_RETRY_COUNT`).

### 4.2. Backend (Laravel 12 / PHP)
- **Class (Model, Controller, Service,...):** `PascalCase`, số ít (VD: `UserController`).
- **Biến & Thuộc tính / Method:** `camelCase` (VD: `$userList`, `getActiveUsers()`).
- **Database:** Table là `snake_case` số nhiều (`users`), Column là `snake_case` số ít (`first_name`). Tham chiếu Model dùng `{model}_id`.
- **Routes:** `kebab-case`, danh từ số nhiều (VD: `/api/v1/user-profiles`).

### 4.3. Frontend (Vue 3 / TS / InertiaJS)
- **Component File (`.vue`):** `PascalCase`, multi-word (VD: `UserProfile.vue`).
- **Sử dụng Component / Khai báo Props:** `PascalCase` cho khai báo (`<UserProfile />`), `camelCase` trong script, `kebab-case` ở template HTML.
- **Biến & Hàm (Script Setup):** `camelCase`.
- **Composables:** Dùng `use` + `PascalCase` (VD: `useAuth()`).
- **Emit Events:** Dùng `camelCase` theo hành động (VD: `updateValue`, `success`).
- **CSS Classes:** `kebab-case` (BEM / Utility format).

---

_Last updated: March 2026_
