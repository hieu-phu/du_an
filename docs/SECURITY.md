# 🔒 Quy Định Bảo Mật (Security Rules)

Tất cả các developer tham gia phát triển dự án hoặc clone dự án từ BaseAsfy cần tuân thủ nghiêm ngặt các quy tắc bảo mật dưới đây để đảm bảo an toàn cho hệ thống.

## 1. Backend (Laravel)

### 1.1. Validation & Input Handling

- **Tuyệt đối không dùng `$request->all()`** để lưu trực tiếp vào cơ sở dữ liệu. Chỉ lấy các trường cần thiết thông qua `$request->validated()` (khi dùng FormRequest) hoặc `$request->only()`.
- **Luôn luôn sử dụng FormRequest** (`app/Http/Requests`) để tách biệt logic kiểm tra tính hợp lệ của dữ liệu khỏi Controller.
- Làm sạch (Sanitize) file tải lên: Validate chặt chẽ dung lượng và `mimes` (định dạng file) khi lưu file. Không lưu các file thực thi (php, exe, sh...).

### 1.2. Database & SQL Injection

- **Không sử dụng Raw SQL Query** nối chuỗi thủ công (`DB::select("SELECT * FROM users WHERE email = '".$email."'")`).
- Ưu tiên sử dụng Eloquent ORM và Query Builder vì Laravel đã bind param an toàn.
- Ngăn chặn **Mass Assignment**: Luôn khai báo `$fillable` (danh sách cột được phép mass-assign) hoặc `$guarded` trong Models. Đừng bao giờ đặt `$guarded = []` nếu không thực sự hiểu ngữ cảnh.

### 1.3. Authorization & Permissions

- Áp dụng kiểm tra quyền truy cập (Authorization) bằng Spatie Permission hoặc Laravel Policies cho **mọi thao tác thay đổi dữ liệu (POST, PUT, DELETE)**.
- **Data Isolation (Cô lập dữ liệu)**: Luôn đảm bảo rằng user chỉ có thể xem/sửa được record thuộc về công ty (`company_id`) hoặc thuộc về chính user đó. Không bao giờ trust `company_id` từ ngoại vi gửi lên (mà phải lấy tự động từ `auth()->user()->company_id`).

### 1.4. Quản lý Credentials và `.env`

- Tuyệt đối không hard-code các thông tin nhạy cảm (mật khẩu, API key, Secret token) trực tiếp trong source code. Luôn dùng helper `env()` (thông qua file config).
- Không được commit file `.env` lên Git.

### 1.5. API & Rate Limiting

- Các API endpoints nên được gắn middleware `throttle` (Rate Limiting) để tránh các cuộc tấn công DDoS và Brute-force.
- Thông báo lỗi (Error messages) trả về cho API không được in trực tiếp exception nội bộ (stack trace) trên production. Chỉ bật `APP_DEBUG=true` trên môi trường dev.

## 2. Frontend (Vue 3 / InertiaJS)

### 2.1. Phòng chống XSS (Cross-Site Scripting)

- Hạn chế tối đa việc sử dụng directive `v-html`. Nếu buộc phải render mã HTML từ người dùng nhập (ví dụ: trình soạn thảo văn bản), **phải làm sạch bằng các thư viện như DOMPurify** trước khi render.
- Blade và Vue tự động escape HTML khi dùng `{{ }}` và `{{ }}`. Đừng vô tình override tính năng này.

### 2.2. Biến Môi Trường (Environment Variables)

- Chỉ thêm prefix `VITE_` vào các biến `.env` mà bạn **chấp nhận công khai toàn bộ** cho phía client (ví dụ: `VITE_PUSHER_APP_KEY`).
- Tuyệt đối không đặt prefix `VITE_` cho các khóa bảo mật secret key, mật khẩu database.

### 2.3. CSRF & State

- Phía Frontend sử dụng Inertia đã được Laravel tự động cấu trúc CSRF Token trong Middleware bảo mật. Đảm bảo tất cả custom Axios requests (trong thư mục `utils/api.ts`) luôn kế thừa header từ thẻ `<meta name="csrf-token">` hoặc cookie `XSRF-TOKEN` nếu có ghi đè custom logic.

## 3. Hệ thống & Packages

- Chạy `composer audit` và `npm audit` thường xuyên để phát hiện các lỗ hổng bảo mật từ thư viện (3rd-party dependencies) đã dùng.
- Thường xuyên update bản patch security của Laravel Framework hoặc Vue3/Vite.
