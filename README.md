# 🚀 BaseAsfy Starter Kit

Dự án Base sử dụng **Laravel 12**, **Vue 3**, **Inertia.js** và **Tailwind CSS**.
Dùng làm repository khởi tạo (Starter Kit) cho các dự án ERP, CRM, Web Application mới.

## 📋 Yêu cầu hệ thống

- PHP ^8.2
- Node.js >= 18
- MySQL >= 8.0
- Composer >= 2.x

## 🔧 Hướng dẫn cài đặt

1. **Clone repository:**

    ```bash
    git clone -b Vue https://github.com/Asfy-Tech/BaseAsfy.git {ten_du_an}
    cd {ten_du_an}
    ```

2. **Cài đặt thư viện Backend & Frontend:**

    ```bash
    composer install
    npm install
    ```

3. **Cấu hình môi trường:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    > Cập nhật thông tin DB database/reverb/broadcast trong file `.env`.

4. **Chạy Migration & Seed:**

    ```bash
    php artisan migrate --seed
    ```

5. **Chạy Môi trường Dev:**
   Dự án đã sử dụng Laravel Concurrently qua scripts setup sẵn, chạy đồng thời Vite + Queue + Reverb + Serve:
    ```bash
    npm run dev
    ```

## 📖 Tài Liệu Tham Khảo

- Sơ đồ kiến trúc / Flow dữ liệu: [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)
- Kiến trúc Frontend Components: [docs/FRONTEND_COMPONENTS.md](docs/FRONTEND_COMPONENTS.md)
- Quy định Bảo Mật (Security Rules): [docs/SECURITY.md](docs/SECURITY.md)
- Training Roadmap (Kế hoạch đào tạo): [TRAINING_ROADMAP.md](TRAINING_ROADMAP.md)

## 📦 Các Tool Tích hợp sẵn

- Authentication: Laravel Sanctum + Socialite (Google OAuth)
- Frontend SPA: InertiaJS + Vue 3
- UI Lib: Tailwind v4 + PrimeVue + Lucide icons
- File Manager: LFM (unisharp)
- Charts: ApexCharts & ChartJS
- Realtime: Laravel Reverb
