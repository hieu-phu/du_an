# PRACTICE MINITASK — BaseAsfy

> Tài liệu yêu cầu chức năng mới cho dự án BaseAsfy.
> Kiến trúc bắt buộc: `Route → Middleware → Controller → Service → Repository → Model`

---

## Mục lục

- [Module 1: Quản lý Khách hàng](#module-1-quản-lý-khách-hàng)
  - [1.1. Quản lý Khách hàng (Customers)](#11-quản-lý-khách-hàng-customers)
  - [1.2. Quản lý Đơn hàng (Orders)](#12-quản-lý-đơn-hàng-orders)
- [Module 2: Quản lý Kho](#module-2-quản-lý-kho)
  - [2.1. Quản lý Sản phẩm (Products)](#21-quản-lý-sản-phẩm-products)
  - [2.2. Quản lý Tồn kho (Inventory)](#22-quản-lý-tồn-kho-inventory)

---

## Module 1: Quản lý Khách hàng

### 1.1. Quản lý Khách hàng (Customers)

#### Mô tả

Quản lý thông tin khách hàng của doanh nghiệp, bao gồm khách hàng cá nhân và doanh nghiệp. Hỗ trợ CRUD đầy đủ, phân loại khách hàng, liên kết với đơn hàng.

#### Database Schema — `customers`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `code` | varchar(20) | UNIQUE, NOT NULL | Mã khách hàng (auto-gen: `KH-000001`) |
| `name` | varchar(255) | NOT NULL | Tên khách hàng / Tên công ty |
| `type` | enum('individual','business') | NOT NULL, DEFAULT 'individual' | Loại: Cá nhân / Doanh nghiệp |
| `email` | varchar(255) | NULLABLE, UNIQUE | Email liên hệ |
| `phone` | varchar(20) | NOT NULL | Số điện thoại chính |
| `phone_secondary` | varchar(20) | NULLABLE | Số điện thoại phụ |
| `tax_code` | varchar(20) | NULLABLE | Mã số thuế (bắt buộc nếu `type = business`) |
| `address` | text | NULLABLE | Địa chỉ |
| `province` | varchar(100) | NULLABLE | Tỉnh/Thành phố |
| `district` | varchar(100) | NULLABLE | Quận/Huyện |
| `ward` | varchar(100) | NULLABLE | Phường/Xã |
| `contact_person` | varchar(255) | NULLABLE | Người liên hệ (cho KH doanh nghiệp) |
| `contact_phone` | varchar(20) | NULLABLE | SĐT người liên hệ |
| `notes` | text | NULLABLE | Ghi chú |
| `status` | enum('active','inactive') | NOT NULL, DEFAULT 'active' | Trạng thái |
| `creater_id` | bigint unsigned | FK → users.id, NULLABLE | Người tạo |
| `created_at` | timestamp | — | Ngày tạo |
| `updated_at` | timestamp | — | Ngày cập nhật |

**Indexes:** `code`, `phone`, `email`, `status`, `type`, `province`

#### Yêu cầu Backend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| B1.1 | Model `Customer` | `$fillable`, `$casts`, relationships: `hasMany(Order)`, `belongsTo(User, 'creater_id')`. Scopes: `scopeActive`, `scopeByType` |
| B1.2 | Migration | Tạo bảng `customers` với schema trên. Có `$table->comment()` cho các cột quan trọng |
| B1.3 | `CustomerRepository` | Extends `BaseRepository`. Methods: `getListPaginated(filters, perPage)` — hỗ trợ filter theo `search` (name/code/phone/email), `status`, `type`, `province`. Sắp xếp theo `created_at DESC` |
| B1.4 | `CustomerService` | Extends `BaseService`. Methods: `getListPaginated()`, `createCustomer()` (auto-gen `code`), `updateCustomer()`, `toggleStatus()`. Dùng `handleTransaction()` cho create/update |
| B1.5 | `StoreCustomerRequest` | Validate: `name` (required, max:255), `type` (required, in:individual,business), `phone` (required, regex VN), `email` (nullable, email, unique:customers), `tax_code` (required_if:type,business). Authorization check |
| B1.6 | `UpdateCustomerRequest` | Tương tự Store nhưng `email` unique ignore current ID. `password` không cần |
| B1.7 | WEB Controller | `index()` → Inertia render `Customer/Index`, `store()`, `update()`, `toggleStatus()`. Dùng `CustomerService`, KHÔNG viết Eloquent trực tiếp |
| B1.8 | API Controller | Endpoint REST cho mobile/external: `GET /api/customers`, `POST /api/customers`, `PUT /api/customers/{id}`. Response format chuẩn `sendSuccess/sendError` |
| B1.9 | Routes | WEB: `prefix('customers')->name('web.customers.')` — index, store, update, toggle. API: `prefix('v1/customers')` |
| B1.10 | Register DI | Đăng ký `CustomerRepository` trong `RepositoryServiceProvider` |

#### Yêu cầu Frontend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| F1.1 | Page `Customer/Index.vue` | Trang danh sách khách hàng dùng `AdminLayout`. Sử dụng `DataTable`, `Pagination`, `SearchPage` giống `User/Index.vue` |
| F1.2 | Columns DataTable | Khách hàng (name + code), Loại (badge individual/business), Liên hệ (phone + email), Địa chỉ (province/district), Trạng thái (badge active/inactive) |
| F1.3 | Filters | Tìm kiếm (name/code/phone), Loại KH (select: Tất cả/Cá nhân/Doanh nghiệp), Trạng thái (select: Tất cả/Active/Inactive) |
| F1.4 | `CustomerFormModal.vue` | Modal thêm/sửa khách hàng. Các field theo schema. Show/hide `tax_code` và `contact_person` khi `type = business`. Dùng `useValidation()` composable |
| F1.5 | Actions | Nút Chỉnh sửa (mở modal edit), Nút Toggle trạng thái (active ↔ inactive) |
| F1.6 | Auto-gen code | Mã KH tự động generate từ backend, frontend hiển thị read-only khi edit |
| F1.7 | Conditional form | Khi chọn `type = business` → hiện thêm: Mã số thuế, Người liên hệ, SĐT người liên hệ |
| F1.8 | Toast notification | Dùng `vue3-toastify` cho success/error messages |

---

### 1.2. Quản lý Đơn hàng (Orders)

#### Mô tả

Quản lý đơn đặt hàng từ khách hàng. Mỗi đơn hàng thuộc 1 khách hàng, chứa nhiều sản phẩm (order details). Hỗ trợ quy trình: Tạo → Xác nhận → Đang xử lý → Hoàn thành / Hủy.

#### Database Schema — `orders`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `code` | varchar(30) | UNIQUE, NOT NULL | Mã đơn hàng (auto-gen: `DH-YYYYMMDD-0001`) |
| `customer_id` | bigint unsigned | FK → customers.id, NOT NULL | Khách hàng |
| `order_date` | date | NOT NULL | Ngày đặt hàng |
| `delivery_date` | date | NULLABLE | Ngày giao hàng dự kiến |
| `status` | enum('draft','confirmed','processing','completed','cancelled') | NOT NULL, DEFAULT 'draft' | Trạng thái đơn hàng |
| `subtotal` | decimal(15,2) | NOT NULL, DEFAULT 0 | Tổng tiền hàng |
| `discount_percent` | decimal(5,2) | DEFAULT 0 | % Chiết khấu |
| `discount_amount` | decimal(15,2) | DEFAULT 0 | Số tiền chiết khấu |
| `tax_percent` | decimal(5,2) | DEFAULT 0 | % Thuế VAT |
| `tax_amount` | decimal(15,2) | DEFAULT 0 | Số tiền thuế |
| `total_amount` | decimal(15,2) | NOT NULL, DEFAULT 0 | Tổng thanh toán |
| `payment_method` | enum('cash','transfer','cod') | DEFAULT 'cash' | Phương thức thanh toán |
| `payment_status` | enum('unpaid','partial','paid') | DEFAULT 'unpaid' | Trạng thái thanh toán |
| `shipping_address` | text | NULLABLE | Địa chỉ giao hàng |
| `notes` | text | NULLABLE | Ghi chú |
| `creater_id` | bigint unsigned | FK → users.id, NULLABLE | Người tạo |
| `confirmed_by` | bigint unsigned | FK → users.id, NULLABLE | Người xác nhận |
| `confirmed_at` | timestamp | NULLABLE | Thời điểm xác nhận |
| `created_at` | timestamp | — | Ngày tạo |
| `updated_at` | timestamp | — | Ngày cập nhật |

**Indexes:** `code`, `customer_id`, `status`, `payment_status`, `order_date`, `creater_id`

#### Database Schema — `order_details`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `order_id` | bigint unsigned | FK → orders.id, ON DELETE CASCADE | Đơn hàng |
| `product_id` | bigint unsigned | FK → products.id, NOT NULL | Sản phẩm |
| `product_name` | varchar(255) | NOT NULL | Tên SP tại thời điểm đặt (snapshot) |
| `product_code` | varchar(50) | NOT NULL | Mã SP tại thời điểm đặt (snapshot) |
| `unit` | varchar(50) | NOT NULL | Đơn vị tính |
| `quantity` | decimal(12,2) | NOT NULL | Số lượng |
| `unit_price` | decimal(15,2) | NOT NULL | Đơn giá |
| `discount_percent` | decimal(5,2) | DEFAULT 0 | % Chiết khấu dòng |
| `discount_amount` | decimal(15,2) | DEFAULT 0 | Tiền CK dòng |
| `line_total` | decimal(15,2) | NOT NULL | Thành tiền (`quantity * unit_price - discount_amount`) |
| `notes` | text | NULLABLE | Ghi chú dòng |
| `created_at` | timestamp | — | |
| `updated_at` | timestamp | — | |

**Indexes:** `order_id`, `product_id`

#### Yêu cầu Backend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| B2.1 | Model `Order` | Relationships: `belongsTo(Customer)`, `hasMany(OrderDetail)`, `belongsTo(User, 'creater_id')`, `belongsTo(User, 'confirmed_by')`. Scopes: `scopeByStatus`, `scopeByDateRange`, `scopeByCustomer`. Accessors: `getFormattedTotalAttribute` |
| B2.2 | Model `OrderDetail` | Relationships: `belongsTo(Order)`, `belongsTo(Product)`. Accessor: `getLineTotalAttribute` (auto-calc) |
| B2.3 | Migration | 2 migrations: `create_orders_table`, `create_order_details_table` (với cascading FK) |
| B2.4 | `OrderRepository` | `getListPaginated(filters, perPage)` — filter: `search` (code), `customer_id`, `status`, `payment_status`, `date_range`. Eager load `customer`, `details.product`, `creater`. Sắp xếp: `order_date DESC` |
| B2.5 | `OrderDetailRepository` | `getByOrderId(orderId)`, `createMany(orderId, details[])`, `deleteByOrderId(orderId)` |
| B2.6 | `OrderService` | `createOrder(data)` — Transaction: tạo order + details array, auto-calc subtotal/tax/total, auto-gen code. `updateOrder()` — xóa details cũ → tạo mới (replace strategy). `updateStatus(order, newStatus)` — validate state transition (draft→confirmed→processing→completed, chỉ draft→cancelled). `getListPaginated()`, `getOrderDetail(id)` |
| B2.7 | `StoreOrderRequest` | Validate: `customer_id` (required, exists:customers,id), `order_date` (required, date), `details` (required, array, min:1), `details.*.product_id` (required, exists:products,id), `details.*.quantity` (required, numeric, min:0.01), `details.*.unit_price` (required, numeric, min:0) |
| B2.8 | `UpdateOrderRequest` | Tương tự Store. Chỉ cho phép update khi `status = draft` |
| B2.9 | WEB Controller | `index()` → Inertia `Order/Index`, `show(order)` → Inertia `Order/Show`, `store()`, `update()`, `updateStatus()` |
| B2.10 | API Controller | CRUD endpoints chuẩn REST |
| B2.11 | Routes | WEB: `prefix('orders')->name('web.orders.')`. API: `prefix('v1/orders')` |
| B2.12 | Register DI | `OrderRepository`, `OrderDetailRepository` trong `RepositoryServiceProvider` |

#### Yêu cầu Frontend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| F2.1 | Page `Order/Index.vue` | Danh sách đơn hàng: Mã ĐH, Khách hàng, Ngày đặt, Tổng tiền, Trạng thái (badge 5 màu), TT Thanh toán |
| F2.2 | Filters | Tìm kiếm (mã ĐH), Khách hàng (select searchable), Trạng thái (select), TT Thanh toán (select), Khoảng thời gian (date range) |
| F2.3 | Page `Order/Show.vue` | Chi tiết đơn hàng: Thông tin chung (header) + Bảng chi tiết sản phẩm (details table) + Summary (subtotal, discount, tax, total). Nút actions tuỳ status |
| F2.4 | `OrderFormModal.vue` hoặc `Order/Create.vue` | Form tạo/sửa đơn hàng. Chọn khách hàng (searchable dropdown), ngày đặt, thêm sản phẩm vào bảng (dynamic rows). Auto-calc line_total, subtotal, tax, total |
| F2.5 | Dynamic detail rows | Nút "Thêm sản phẩm" → thêm row mới. Mỗi row: Chọn SP (searchable), SL, đơn giá (auto-fill từ SP), CK%, thành tiền (auto-calc). Nút xóa dòng |
| F2.6 | Status flow UI | Hiển thị stepper/timeline cho luồng trạng thái. Nút action phù hợp: "Xác nhận" (draft→confirmed), "Xử lý" (confirmed→processing), "Hoàn thành" (processing→completed), "Hủy" (draft→cancelled) |
| F2.7 | Format tiền | Hiển thị VND format (`Intl.NumberFormat('vi-VN')`) |
| F2.8 | Validation frontend | Dùng `useValidation()` — validate customer, order_date, ít nhất 1 dòng SP, quantity > 0, unit_price >= 0 |

---

## Module 2: Quản lý Kho

### 2.1. Quản lý Sản phẩm (Products)

#### Mô tả

Quản lý danh mục sản phẩm/hàng hóa. Mỗi sản phẩm có mã, tên, phân loại theo danh mục, đơn vị tính, giá bán, giá nhập. Liên kết với tồn kho và đơn hàng.

#### Database Schema — `product_categories`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `name` | varchar(255) | NOT NULL | Tên danh mục |
| `slug` | varchar(255) | UNIQUE, NOT NULL | Slug |
| `parent_id` | bigint unsigned | FK → product_categories.id, NULLABLE | Danh mục cha (hỗ trợ đa cấp) |
| `description` | text | NULLABLE | Mô tả |
| `sort_order` | int | DEFAULT 0 | Thứ tự sắp xếp |
| `status` | enum('active','inactive') | DEFAULT 'active' | Trạng thái |
| `created_at` | timestamp | — | |
| `updated_at` | timestamp | — | |

#### Database Schema — `products`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `code` | varchar(50) | UNIQUE, NOT NULL | Mã sản phẩm (auto-gen: `SP-000001`) |
| `name` | varchar(255) | NOT NULL | Tên sản phẩm |
| `slug` | varchar(255) | UNIQUE, NOT NULL | Slug (auto-gen từ name) |
| `category_id` | bigint unsigned | FK → product_categories.id, NULLABLE | Danh mục |
| `description` | text | NULLABLE | Mô tả |
| `unit` | varchar(50) | NOT NULL | Đơn vị tính (cái, kg, hộp, ...) |
| `purchase_price` | decimal(15,2) | DEFAULT 0 | Giá nhập |
| `selling_price` | decimal(15,2) | DEFAULT 0 | Giá bán |
| `min_stock` | int | DEFAULT 0 | Tồn kho tối thiểu (cảnh báo) |
| `max_stock` | int | DEFAULT 0 | Tồn kho tối đa |
| `image` | varchar(255) | NULLABLE | Ảnh sản phẩm |
| `thumbnail` | varchar(255) | NULLABLE | Ảnh thu nhỏ |
| `barcode` | varchar(100) | NULLABLE, UNIQUE | Mã vạch |
| `weight` | decimal(10,2) | NULLABLE | Trọng lượng (kg) |
| `is_active` | boolean | DEFAULT true | Đang kinh doanh |
| `notes` | text | NULLABLE | Ghi chú |
| `creater_id` | bigint unsigned | FK → users.id, NULLABLE | Người tạo |
| `created_at` | timestamp | — | |
| `updated_at` | timestamp | — | |

**Indexes:** `code`, `name`, `category_id`, `barcode`, `is_active`, `selling_price`

#### Yêu cầu Backend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| B3.1 | Model `ProductCategory` | Self-referencing: `belongsTo(self, 'parent_id')`, `hasMany(self, 'parent_id')`, `hasMany(Product)`. Scope: `scopeActive`, `scopeRoots` (where parent_id = null) |
| B3.2 | Model `Product` | Relationships: `belongsTo(ProductCategory)`, `hasMany(OrderDetail)`, `hasOne(Inventory)`. Scopes: `scopeActive`, `scopeByCategory`, `scopeLowStock` (so sánh inventory.quantity vs min_stock). Casts: `is_active → boolean`, `purchase_price → decimal:2`, `selling_price → decimal:2` |
| B3.3 | Migrations | `create_product_categories_table`, `create_products_table` |
| B3.4 | `ProductCategoryRepository` | `getListTree()` — trả về structure cha-con. `getActiveList()` |
| B3.5 | `ProductRepository` | `getListPaginated(filters, perPage)` — filter: `search` (name/code/barcode), `category_id`, `is_active`, `price_range`. Eager load `category`, `inventory`. Sắp xếp: `name ASC` |
| B3.6 | `ProductCategoryService` | CRUD danh mục. Validate không cho xóa danh mục có con hoặc có sản phẩm |
| B3.7 | `ProductService` | `createProduct(data, imageFile?)` — auto-gen code + slug, upload image + thumbnail (tương tự UserService avatar). `updateProduct()`, `toggleActive()`. Dùng `handleTransaction()` |
| B3.8 | `StoreProductRequest` | Validate: `name` (required, max:255), `unit` (required, max:50), `category_id` (nullable, exists), `selling_price` (required, numeric, min:0), `purchase_price` (nullable, numeric, min:0), `image` (nullable, image, max:2048kb), `barcode` (nullable, unique:products) |
| B3.9 | `UpdateProductRequest` | Tương tự Store, barcode unique ignore current |
| B3.10 | WEB Controller | `index()`, `store()`, `update()`, `toggleActive()` |
| B3.11 | API Controller | CRUD + `GET /api/v1/products/search?q=` (cho chọn SP trong đơn hàng) |
| B3.12 | Routes | WEB: `prefix('products')->name('web.products.')`. API: `prefix('v1/products')` |
| B3.13 | Register DI | `ProductRepository`, `ProductCategoryRepository` trong `RepositoryServiceProvider` |

#### Yêu cầu Frontend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| F3.1 | Page `Product/Index.vue` | Danh sách SP: Image (thumbnail hoặc placeholder), Mã SP + Tên, Danh mục, ĐVT, Giá bán, Tồn kho (từ inventory), Trạng thái |
| F3.2 | Filters | Tìm kiếm (tên/mã/barcode), Danh mục (select tree), Trạng thái (Active/Inactive), Khoảng giá |
| F3.3 | `ProductFormModal.vue` | Modal thêm/sửa SP. Upload ảnh (preview trước khi upload). Fields theo schema. Chọn danh mục dạng tree-select |
| F3.4 | Ảnh sản phẩm | Upload + preview. Hiển thị thumbnail trong DataTable. Placeholder icon nếu không có ảnh |
| F3.5 | Format giá | Hiển thị VND format. Input chấp nhận formatNumber khi gõ |
| F3.6 | Low stock highlight | Nếu tồn kho < min_stock → highlight đỏ/cảnh báo trong DataTable |
| F3.7 | Actions | Chỉnh sửa, Toggle Active/Inactive |
| F3.8 | Quản lý Danh mục SP | Sub-page hoặc modal riêng: CRUD danh mục (hỗ trợ chọn parent → tạo cây). Có thể là `Product/Categories.vue` |

---

### 2.2. Quản lý Tồn kho (Inventory)

#### Mô tả

Theo dõi số lượng tồn kho theo từng sản phẩm. Ghi nhận lịch sử xuất nhập kho (stock movements). Cảnh báo khi tồn kho thấp/vượt mức.

#### Database Schema — `inventories`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `product_id` | bigint unsigned | FK → products.id, UNIQUE, NOT NULL | Sản phẩm (1:1) |
| `quantity` | decimal(12,2) | NOT NULL, DEFAULT 0 | Số lượng tồn hiện tại |
| `reserved_quantity` | decimal(12,2) | DEFAULT 0 | Số lượng đã đặt (chưa xuất) |
| `available_quantity` | decimal(12,2) | GENERATED (quantity - reserved_quantity) | Số lượng khả dụng (virtual/stored) |
| `last_stock_in` | timestamp | NULLABLE | Lần nhập kho gần nhất |
| `last_stock_out` | timestamp | NULLABLE | Lần xuất kho gần nhất |
| `created_at` | timestamp | — | |
| `updated_at` | timestamp | — | |

**Indexes:** `product_id` (unique), `quantity`

#### Database Schema — `stock_movements`

| Column | Type | Constraints | Mô tả |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, AUTO_INCREMENT | ID |
| `product_id` | bigint unsigned | FK → products.id, NOT NULL | Sản phẩm |
| `type` | enum('in','out','adjustment','return') | NOT NULL | Loại: Nhập / Xuất / Điều chỉnh / Trả hàng |
| `quantity` | decimal(12,2) | NOT NULL | Số lượng thay đổi (luôn dương) |
| `quantity_before` | decimal(12,2) | NOT NULL | Tồn trước khi thay đổi |
| `quantity_after` | decimal(12,2) | NOT NULL | Tồn sau khi thay đổi |
| `reference_type` | varchar(100) | NULLABLE | Loại chứng từ tham chiếu (`order`, `manual`, `return`) |
| `reference_id` | bigint unsigned | NULLABLE | ID chứng từ tham chiếu |
| `reason` | text | NULLABLE | Lý do (bắt buộc nếu `type = adjustment`) |
| `notes` | text | NULLABLE | Ghi chú |
| `creater_id` | bigint unsigned | FK → users.id, NULLABLE | Người thực hiện |
| `created_at` | timestamp | — | |
| `updated_at` | timestamp | — | |

**Indexes:** `product_id`, `type`, `reference_type + reference_id`, `created_at`, `creater_id`

#### Yêu cầu Backend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| B4.1 | Model `Inventory` | Relationships: `belongsTo(Product)`. Scope: `scopeLowStock` (quantity <= product.min_stock), `scopeOverStock` (quantity >= product.max_stock) |
| B4.2 | Model `StockMovement` | Relationships: `belongsTo(Product)`, `belongsTo(User, 'creater_id')`. Morph-like reference: `reference_type` + `reference_id`. Scope: `scopeByType`, `scopeByDateRange`, `scopeByProduct` |
| B4.3 | Migrations | `create_inventories_table`, `create_stock_movements_table` |
| B4.4 | `InventoryRepository` | `getByProductId(productId)`, `getListPaginated(filters, perPage)` — filter: `search` (product name/code), `stock_status` (low/normal/over), `category_id`. Eager load `product.category` |
| B4.5 | `StockMovementRepository` | `getByProductPaginated(productId, perPage)`, `getListPaginated(filters, perPage)` — filter: `product_id`, `type`, `date_range`, `creater_id` |
| B4.6 | `InventoryService` | **Core methods**: `stockIn(productId, quantity, referenceType?, referenceId?, notes?)` — tăng tồn kho + tạo movement. `stockOut(productId, quantity, ...)` — giảm tồn kho (validate đủ hàng) + tạo movement. `adjustStock(productId, newQuantity, reason)` — điều chỉnh tồn + tạo movement type=adjustment. **Auto-init**: khi tạo product mới → tự tạo record inventory. **Validation**: `stockOut` phải kiểm tra `available_quantity >= quantity`. Tất cả dùng `handleTransaction()` |
| B4.7 | `StockInRequest` | Validate: `product_id` (required, exists), `quantity` (required, numeric, min:0.01), `notes` (nullable, max:500) |
| B4.8 | `StockAdjustmentRequest` | Validate: `product_id` (required, exists), `quantity` (required, numeric, min:0), `reason` (required, max:500) |
| B4.9 | WEB Controller | `index()` → Inertia `Inventory/Index` (dashboard tồn kho), `movements(product)` → history, `stockIn()`, `stockOut()`, `adjustStock()` |
| B4.10 | API Controller | `GET /api/v1/inventory`, `POST /api/v1/inventory/stock-in`, `POST /api/v1/inventory/stock-out`, `POST /api/v1/inventory/adjust`, `GET /api/v1/inventory/{product}/movements` |
| B4.11 | Routes | WEB: `prefix('inventory')->name('web.inventory.')`. API: `prefix('v1/inventory')` |
| B4.12 | Register DI | `InventoryRepository`, `StockMovementRepository` trong `RepositoryServiceProvider` |
| B4.13 | Event kết nối | Khi đơn hàng chuyển `confirmed → processing` → auto `stockOut` cho tất cả details (reserved). Khi đơn hàng `cancelled` → auto trả lại tồn kho (`stockIn` type=return). Khi tạo Product mới → auto tạo Inventory record (quantity=0) |

#### Yêu cầu Frontend

| # | Yêu cầu | Chi tiết |
|---|---------|---------|
| F4.1 | Page `Inventory/Index.vue` | Dashboard tồn kho: DataTable hiển thị tất cả SP + tồn kho. Columns: SP (image+name+code), Danh mục, ĐVT, Tồn kho, Đã đặt, Khả dụng, Min/Max, Trạng thái tồn (badge: Thấp/Bình thường/Vượt) |
| F4.2 | Stock status badges | 🔴 **Thấp** (quantity <= min_stock) → badge đỏ. 🟢 **Bình thường** → badge xanh. 🟡 **Vượt mức** (quantity >= max_stock) → badge vàng. Nếu quantity = 0 → badge "Hết hàng" đỏ đậm |
| F4.3 | Filters | Tìm kiếm SP, Danh mục (select), Trạng thái tồn (Tất cả/Thấp/Bình thường/Vượt/Hết hàng) |
| F4.4 | Actions tồn kho | Nút "Nhập kho" → Modal nhập: SP (read-only nếu từ row), Số lượng, Ghi chú. Nút "Xuất kho" → Modal xuất: tương tự + validate tồn đủ. Nút "Điều chỉnh" → Modal: Số lượng mới, Lý do (bắt buộc) |
| F4.5 | `StockMovementModal.vue` | Modal chung cho nhập/xuất/điều chỉnh. Props: `type` ('in', 'out', 'adjustment'). Hiển thị form tương ứng |
| F4.6 | Lịch sử xuất nhập | Click vào SP → Drawer hoặc trang `Inventory/Movements.vue` hiển thị lịch sử. DataTable: Ngày, Loại (badge in/out/adjustment/return), SL, Tồn trước, Tồn sau, Người thực hiện, Ghi chú |
| F4.7 | Summary cards | Trên đầu trang Index: 4 cards tổng hợp — Tổng SP, Tồn thấp (count), Hết hàng (count), Giá trị tồn kho ước tính (Σ quantity × purchase_price) |
| F4.8 | Realtime update | Khi có stock movement mới → auto refresh DataTable (dùng Inertia reload hoặc Echo event) |

---

## Checklist tạo files (theo thứ tự)

### Phase 1: Database & Models

```
[ ] 1.  Migration: create_product_categories_table
[ ] 2.  Migration: create_products_table
[ ] 3.  Migration: create_inventories_table
[ ] 4.  Migration: create_stock_movements_table
[ ] 5.  Migration: create_customers_table
[ ] 6.  Migration: create_orders_table
[ ] 7.  Migration: create_order_details_table
[ ] 8.  Model: ProductCategory
[ ] 9.  Model: Product
[ ] 10. Model: Inventory
[ ] 11. Model: StockMovement
[ ] 12. Model: Customer
[ ] 13. Model: Order
[ ] 14. Model: OrderDetail
```

### Phase 2: Repositories

```
[ ] 15. ProductCategoryRepository (extends BaseRepository)
[ ] 16. ProductRepository (extends BaseRepository)
[ ] 17. InventoryRepository (extends BaseRepository)
[ ] 18. StockMovementRepository (extends BaseRepository)
[ ] 19. CustomerRepository (extends BaseRepository)
[ ] 20. OrderRepository (extends BaseRepository)
[ ] 21. OrderDetailRepository (extends BaseRepository)
[ ] 22. Đăng ký tất cả trong RepositoryServiceProvider
```

### Phase 3: Services

```
[ ] 23. ProductCategoryService (extends BaseService)
[ ] 24. ProductService (extends BaseService)
[ ] 25. InventoryService (extends BaseService)
[ ] 26. CustomerService (extends BaseService)
[ ] 27. OrderService (extends BaseService)
```

### Phase 4: Requests & Controllers

```
[ ] 28. StoreCustomerRequest / UpdateCustomerRequest
[ ] 29. StoreProductRequest / UpdateProductRequest
[ ] 30. StoreCategoryRequest / UpdateCategoryRequest
[ ] 31. StoreOrderRequest / UpdateOrderRequest
[ ] 32. StockInRequest / StockOutRequest / StockAdjustmentRequest
[ ] 33. WEB/CustomerController
[ ] 34. WEB/ProductController
[ ] 35. WEB/ProductCategoryController
[ ] 36. WEB/OrderController
[ ] 37. WEB/InventoryController
[ ] 38. API/CustomerController
[ ] 39. API/ProductController
[ ] 40. API/OrderController
[ ] 41. API/InventoryController
[ ] 42. Routes: web.php + api.php
```

### Phase 5: Frontend Pages & Components

```
[ ] 43. Pages/Customer/Index.vue
[ ] 44. components/customers/CustomerFormModal.vue
[ ] 45. Pages/Product/Index.vue
[ ] 46. Pages/Product/Categories.vue (hoặc modal)
[ ] 47. components/products/ProductFormModal.vue
[ ] 48. components/products/CategoryFormModal.vue
[ ] 49. Pages/Order/Index.vue
[ ] 50. Pages/Order/Show.vue
[ ] 51. Pages/Order/Create.vue (hoặc modal)
[ ] 52. components/orders/OrderDetailRows.vue
[ ] 53. Pages/Inventory/Index.vue
[ ] 54. Pages/Inventory/Movements.vue
[ ] 55. components/inventory/StockMovementModal.vue
[ ] 56. components/inventory/StockSummaryCards.vue
```

---

## Quy tắc tuân thủ (nhắc lại)

> ⚠️ **KIẾN TRÚC BẮT BUỘC** — Vi phạm bất kỳ mục nào dưới đây = SAI

| Rule | Mô tả |
|------|-------|
| ❌ Eloquent trong Controller | Controller chỉ gọi Service |
| ❌ Eloquent trong Service | Service chỉ gọi Repository |
| ❌ Business logic trong Model | Model chỉ có relationships, scopes, casts |
| ❌ `$request->all()` | Dùng `$request->validated()` hoặc DTO |
| ❌ Hardcode URL trong Vue | Dùng `route('name')` (Ziggy) |
| ❌ Sửa `components/ui/*` | Tạo component mới |
| ❌ Vue Router | Dùng InertiaJS `router` / `Link` |
| ✅ `<script setup>` | Luôn dùng Composition API |
| ✅ `defineProps<{}>()` | Typed props |
| ✅ `useValidation()` | Validation frontend |
| ✅ `sendSuccess()` / `sendError()` | Response format chuẩn |
| ✅ `handleTransaction()` | Cho create/update có nhiều bước |
