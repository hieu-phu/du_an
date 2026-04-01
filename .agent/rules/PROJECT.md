---
trigger: always_on
---

# GEMINI.md - BaseAsfy Project Rules

> Quy tắc dành riêng cho dự án BaseAsfy — Laravel 12 + Vue 3 (InertiaJS).
> File này **bổ sung** cho quy tắc gốc Antigravity Kit, KHÔNG thay thế.

---

## 📌 PROJECT IDENTITY

| Key | Value |
|-----|-------|
| **Project** | BaseAsfy — Core framework Asfy Tech |
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Frontend** | Vue 3 + InertiaJS + Vuex4 |
| **CSS** | Tailwind CSS v4 |
| **Build** | Vite 7 |
| **Auth** | Laravel Sanctum + Socialite |
| **Realtime** | Laravel Reverb + Pusher + Echo |
| **Permissions** | Spatie Laravel Permission v6 |
| **Database** | MySQL / MariaDB |
| **OS** | Windows (PowerShell) |

---

## 🏗️ KIẾN TRÚC BẮT BUỘC

### Luồng xử lý Request (PHẢI tuân thủ)

```
Route → Middleware (Auth/Role) → Controller → Service → Repository → Model → Database
```

### Quy tắc phân tầng (TUYỆT ĐỐI)

| Tầng | File Location | ĐƯỢC phép | KHÔNG ĐƯỢC phép |
|------|--------------|-----------|-----------------|
| **Controller** | `app/Http/Controllers/` | Nhận request, gọi Service, trả response | ❌ Business logic, ❌ Eloquent query |
| **Service** | `app/Services/` | Business logic, DB Transaction, gọi Repository | ❌ Eloquent query trực tiếp |
| **Repository** | `app/Repositories/` | Eloquent query, DB access | ❌ Business logic, ❌ HTTP response |
| **Model** | `app/Models/` | Relationships, Scopes, Casts, Accessors | ❌ Business logic |
| **Request** | `app/Http/Requests/` | Validation rules, Authorization | ❌ Business logic |
| **DTO** | `app/DTOs/` | Data binding (typed) | ❌ Logic |
| **Resource** | `app/Http/Resources/` | JSON format output | ❌ Logic |

> 🔴 **VI PHẠM:** Viết Eloquent query trong Controller hoặc Service = SAI KIẾN TRÚC.

---

## 📝 QUY TẮC KHI TẠO MODULE MỚI

### Checklist tạo module (VD: `Material`)

```
[ ] 1. Model          → app/Models/Material.php
[ ] 2. Migration      → database/migrations/xxxx_create_materials_table.php
[ ] 3. Repository     → app/Repositories/MaterialRepository.php (extends BaseRepository)
[ ] 4. Service        → app/Services/MaterialService.php (extends BaseService)
[ ] 5. Controller API → app/Http/Controllers/API/MaterialController.php
[ ] 6. Controller WEB → app/Http/Controllers/WEB/MaterialController.php (nếu cần)
[ ] 7. Request        → app/Http/Requests/MaterialRequest.php
[ ] 8. DTO            → app/DTOs/MaterialDTO.php (nếu cần)
[ ] 9. Resource       → app/Http/Resources/MaterialResource.php (nếu cần)
[ ] 10. Register DI   → app/Providers/RepositoryServiceProvider.php
[ ] 11. Routes        → routes/api.php / routes/web.php
```

### Mẫu Repository mới

```php
<?php

namespace App\Repositories;

use App\Models\Material;

class MaterialRepository extends BaseRepository
{
    public function __construct(Material $model)
    {
        parent::__construct($model);
    }

    // Thêm query methods cụ thể ở đây
}
```

### Mẫu Service mới

```php
<?php

namespace App\Services;

use App\Repositories\MaterialRepository;

class MaterialService extends BaseService
{
    public function __construct(
        protected MaterialRepository $materialRepository
    ) {}

    // Business logic ở đây — KHÔNG viết Eloquent query
}
```

### Đăng ký Repository (BẮT BUỘC)

```php
// app/Providers/RepositoryServiceProvider.php
$this->app->singleton(MaterialRepository::class);
```

---

## 🎨 QUY TẮC FRONTEND (Vue 3 + InertiaJS)

### Cấu trúc thư mục

| Thư mục | Mục đích | Ví dụ |
|---------|---------|-------|
| `resources/js/Pages/` | Page components (Inertia render) | `Materials/Index.vue` |
| `resources/js/components/` | Reusable UI components | `DataTable.vue` |
| `resources/js/components/ui/` | Base UI (KHÔNG sửa) | `Button.vue` |
| `resources/js/Layouts/` | Layout chung | `AdminLayout.vue` |
| `resources/js/store/modules/` | Vuex modules | `orderForm.js` |
| `resources/js/utils/` | Helpers, formatters | `api.ts` |

### Quy tắc Vue Component

| Quy tắc | Đúng | Sai |
|---------|------|-----|
| Script setup | `<script setup>` | `<script> export default` |
| Props typing | `defineProps<{}>()` | Không khai báo type |
| State management | Vuex store module | Global reactive variables |
| API calls | `axios` + route helper | Hardcode URL |
| Router | InertiaJS `router` / `Link` | Vue Router |
| Route names | `route('materials.index')` (Ziggy) | `/api/materials` |

### ⛔ KHÔNG SỬA base UI components

```
❌ resources/js/components/ui/*   ← KHÔNG SỬA (base framework)
✅ resources/js/components/*      ← TẠO MỚI hoặc SỬA ở đây
```

---

## 🗃️ QUY TẮC DATABASE

### Migration

| Quy tắc | Ví dụ |
|---------|-------|
| Table name: `snake_case` số nhiều | `materials`, `order_details` |
| Column name: `snake_case` số ít | `unit_price`, `created_at` |
| Foreign key: `{model}_id` | `user_id`, `material_id` |
| Luôn có `$table->timestamps()` | — |
| Luôn có `$table->comment()` cho cột quan trọng | — |
| Index cho cột thường query | `$table->index('status')` |

### Model

| Quy tắc | Chi tiết |
|---------|---------|
| Khai báo `$fillable` đầy đủ | Danh sách rõ ràng |
| Khai báo `$casts` cho date, json, boolean | `'data' => 'array'` |
| Relationships: return type hint | `public function user(): BelongsTo` |
| Scopes: prefix `scope` | `scopeActive($query)` |

---

## 🔐 QUY TẮC BẢO MẬT

| Quy tắc | Bắt buộc |
|---------|---------|
| Validation trong FormRequest | ✅ Luôn |
| Authorize trong FormRequest | ✅ Luôn |
| Eloquent thay raw SQL | ✅ Tránh SQL injection |
| Không hardcode secrets | ✅ Dùng `.env` |
| Sanctum cho API auth | ✅ |
| Spatie permission cho RBAC | ✅ |
| CSRF protection | ✅ Laravel tự xử lý |

---

## 🔄 QUY TẮC BROADCAST / REALTIME

| Quy tắc | Chi tiết |
|---------|---------|
| Channel naming | `user.{id}.{subdomain}.notifications` |
| Event class | Extends `ShouldBroadcast`, implements `broadcastWith()` |
| Frontend listener | `window.Echo.private(channel).listen('.event.name')` |
| Auth channel | Đăng ký trong `routes/channels.php` |

---

## 📏 QUY TẮC ĐẶT TÊN

### Backend (PHP/Laravel)

| Loại | Convention | Ví dụ |
|------|-----------|-------|
| Class | `PascalCase`, số ít | `UserController`, `MaterialService` |
| Method | `camelCase`, động từ + danh từ | `getUserById()`, `createOrder()` |
| Variable | `camelCase` | `$userList`, `$totalAmount` |
| Boolean | `is/has/can/should` prefix | `$isActive`, `$hasPermission` |
| Constant | `SCREAMING_SNAKE_CASE` | `MAX_RETRY_COUNT` |
| Route | `kebab-case`, danh từ số nhiều | `/api/v1/user-profiles` |

### Frontend (Vue/JS)

| Loại | Convention | Ví dụ |
|------|-----------|-------|
| Component file | `PascalCase` | `UserProfile.vue` |
| Component usage | `PascalCase` | `<UserProfile />` |
| Variables/Functions | `camelCase` | `const userName = ref('')` |
| Composables | `use` + `PascalCase` | `useAuth()` |
| Emit events | `camelCase` | `emit('updateValue')` |
| CSS classes | `kebab-case` | `.user-card` |

---

## 📋 CONTROLLER RESPONSE FORMAT

### API Controller — dùng helper methods từ base Controller

```php
// Thành công
return $this->sendSuccess($data, 'Tạo thành công', 201);

// Lỗi
return $this->sendError('Không tìm thấy', null, 404);

// Response format chuẩn:
{
    "success": true|false,
    "message": "...",
    "data": {...}
}
```

### WEB Controller — dùng Inertia render

```php
return Inertia::render('Materials/Index', [
    'materials' => $materials,
    'filters' => $request->only(['search', 'status']),
]);
```

---

## ⚙️ COMMANDS THƯỜNG DÙNG

```powershell
# Chạy dev server (đầy đủ)
composer dev

# Chạy migration
php artisan migrate

# Reset migration (⚠️ xóa data)
php artisan migrate:fresh

# Tạo model + migration
php artisan make:model Material -m

# Clear cache
php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Kiểm tra routes
php artisan route:list

# Chạy tests
php artisan test

# Lint PHP
./vendor/bin/pint

# Build frontend
npm run build
```

---

## 🧪 QUY TẮC TEST

| Loại | Tool | Thư mục |
|------|------|---------|
| Unit Test | PHPUnit | `tests/Unit/` |
| Feature Test | PHPUnit | `tests/Feature/` |
| PHP Lint | Laravel Pint | `./vendor/bin/pint` |

### Mẫu test

```php
public function test_can_create_material(): void
{
    $response = $this->postJson('/api/materials', [
        'name' => 'Test Material',
        'code' => 'MAT-001',
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);
}
```

---

## 🚫 CÁC LỖI THƯỜNG GẶP (TRÁNH)

| ❌ Sai | ✅ Đúng |
|--------|---------|
| Eloquent query trong Controller | Gọi Service → Repository |
| `DB::table()` trong Service | Repository method |
| Hardcode URL trong Vue | `route('name')` (Ziggy) |
| Sửa `components/ui/*` | Tạo component mới kế thừa |
| `$request->all()` truyền thẳng | Dùng DTO hoặc `$request->validated()` |
| Business logic trong Model | Đặt trong Service |
| Mix API + WEB trong 1 controller | Tách `API/` và `WEB/` controllers |
| Quên đăng ký Repository | `RepositoryServiceProvider.php` |

---

## 📦 TECH STACK REFERENCE

### Backend Packages

| Package | Mục đích |
|---------|---------|
| `laravel/sanctum` | API authentication |
| `laravel/socialite` | Social login (Google, etc.) |
| `laravel/reverb` | WebSocket server |
| `spatie/laravel-permission` | Role & Permission |
| `intervention/image` | Image processing |
| `tightenco/ziggy` | JS route helper |
| `laravel/pint` | PHP code style |

### Frontend Packages

| Package | Mục đích |
|---------|---------|
| `@inertiajs/vue3` | SPA routing |
| `vuex` | State management |
| `tailwindcss` v4 | CSS framework |
| `laravel-echo` + `pusher-js` | Realtime |
| `primevue` | UI components |
| `vue3-toastify` | Toast notifications |
| `sweetalert2` | Modal dialogs |
| `apexcharts` / `chart.js` | Charts |
| `ziggy-js` | Laravel route() in JS |

---
