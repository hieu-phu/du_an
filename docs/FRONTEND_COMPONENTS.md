# Quy định thư mục Frontend Components

Trong BaseAsfy (Vue 3 + InertiaJS) nhằm đảm bảo tính cấu trúc chung dễ mở rộng và tái sử dụng, tất cả custom component **bắt buộc** phải được lưu trữ trong thư mục `resources/js/components/{nhóm}/` tương ứng với công năng của chúng.

Quy định cấu trúc:

- `Button/`: Chứa toàn bộ các loại nút (`DropdownButton.vue`, `SubmitButton.vue`, `IconButton.vue`, v.v...).
- `forms/`: Các phần tử dùng trong `<form>` (`InputDate.vue`, `InputMoney.vue`, `FormSelect.vue`, `ErrorForm.vue`, `Datepicker.vue`).
- `tables/`: Tất cả thành phần liên quan đến cấu trúc dạng bảng/grid dữ liệu (`DataTable.vue`, `PaginatorTable.vue`, `LoadingTable.vue`, `Pagination.vue`).
- `modals/`: Chứa các khung hiển thị Dialog, Popup, Slider che màn hình (`Modal.vue`, `CustomModal.vue`, v.v...).
- `cards/`: Container giao diện thẻ/mảnh ghép dữ liệu nhỏ (`InfoCard.vue`, `StatCard.vue`, `MetricCards.vue`).
- `charts/`: Các Component chuyên vẽ biểu đồ (ChartJs, ApexChart wrapper).
- `layout/`: Các thành phần của Layout lớn (Header, Sidebar, Footer, Navigation). **Lưu ý**: Các file Layout khung chuẩn (AdminLayout, PublicLayout) đặt ở `resources/js/Layouts/`.
- `Notifications/`: Hệ thống UI của thông báo, alert, toast container.
- `ui/`: Các element giao diện cơ bản khác không nằm trong các nhóm bên trên và mang tính thiết kế thuần túy (ví dụ: `Tooltip.vue`, `Bagde`, `Spinner`).
- `features/`: Chứa các cụm tổ hợp logic lớn dùng chung liên kết nhiều logic khác (ví dụ: `SearchPage.vue` chứa luồng search phức tạp, Form tích hợp multi component liên quan chuyên biệt).

---

> **Nguyên tắc Reusability:**
> Không bao giờ push code custom thuộc về duy nhất nghiệp vụ của 1 page cụ thể vào `resources/js/components/`.
> Giao diện không tái sử dụng thì phải nằm cùng cấp với Page tương ứng hoặc chia thư mục trong chính Page đó.
