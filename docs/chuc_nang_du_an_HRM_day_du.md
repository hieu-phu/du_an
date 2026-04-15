# Mô tả đầy đủ các chức năng cần làm cho dự án HRM

## 1. Mục tiêu dự án
Xây dựng hệ thống HRM trên nền tảng web để quản lý toàn diện nhân sự, phòng ban, chức vụ, chấm công, dự án, báo cáo và phân quyền trong doanh nghiệp. Hệ thống phục vụ 3 nhóm người dùng chính: **Admin**, **HR**, **Nhân viên**.

---

## 2. Các vai trò trong hệ thống

### 2.1. Admin
- Quản lý toàn bộ hệ thống
- Quản lý nhân sự, phòng ban, chức vụ
- Quản lý dự án và chi tiết triển khai
- Xem tất cả báo cáo
- Duyệt các thay đổi do HR gửi
- Cấu hình website
- Theo dõi thông báo và nhật ký hệ thống

### 2.2. HR
- Quản lý nhân sự
- Quản lý phòng ban
- Quản lý chức vụ
- Xem danh sách dự án
- Theo dõi và xác nhận chấm công
- Xem và xuất báo cáo chấm công toàn bộ nhân sự
- Gửi yêu cầu thay đổi cần Admin duyệt

### 2.3. Nhân viên
- Xem hồ sơ cá nhân
- Xem các dự án đang và đã tham gia
- Thực hiện check-in / check-out
- Xem báo cáo công của bản thân
- Gửi phản hồi / đề xuất tới HR và Admin

---

## 3. Các module chức năng chính

## Module 1. Quản lý tài khoản và phân quyền

### Chức năng cần làm
- Đăng nhập hệ thống
- Đăng xuất hệ thống
- Quản lý tài khoản người dùng
- Khóa / mở tài khoản
- Phân quyền theo vai trò: Admin, HR, Nhân viên
- Kiểm tra quyền truy cập theo từng màn hình và hành động
- Hiển thị menu theo quyền của từng vai trò

### Chức năng quản lý cần bổ sung
- Quản lý danh sách quyền
- Quản lý lịch sử đăng nhập
- Quản lý trạng thái tài khoản online / offline

---

## Module 2. Quản lý nhân sự

### Chức năng cần làm
- Thêm mới nhân sự
- Sửa thông tin nhân sự
- Khóa nhân sự
- Xem danh sách nhân sự
- Lọc nhân sự theo:
  - tên nhân sự
  - ngày bắt đầu làm việc
  - phòng ban
- Xuất danh sách nhân sự ra Excel
- Xuất danh sách nhân sự ra PDF

### Thông tin cần quản lý
- Họ và tên
- Email Gmail
- Avatar lấy theo tài khoản Gmail
- Số điện thoại
- Địa chỉ
- Ngày sinh
- Ngày bắt đầu làm việc
- Lương cơ bản
- Phòng ban
- Chức vụ
- Trạng thái làm việc
- Trạng thái online / offline

### Validate cần làm
- Tên nhân sự không để trống
- Email bắt buộc đúng định dạng Gmail
- Email không được trùng trong database
- Số điện thoại đúng định dạng
- Ngày sinh hợp lệ
- Ngày vào làm hợp lệ

### Chức năng quản lý cần bổ sung
- Quản lý trạng thái nhân sự:
  - đang làm việc
  - tạm nghỉ
  - nghỉ việc
  - bị khóa
- Quản lý lịch sử thay đổi thông tin nhân sự
- Quản lý lịch sử điều chuyển phòng ban
- Quản lý lịch sử thay đổi chức vụ
- Quản lý lịch sử thay đổi lương

---

## Module 3. Quản lý phòng ban

### Chức năng cần làm
- Thêm mới phòng ban
- Sửa phòng ban
- Khóa phòng ban
- Xem danh sách phòng ban

### Thông tin cần quản lý
- Tên phòng ban
- Mô tả
- Số lượng nhân sự thuộc phòng ban
- Trạng thái phòng ban

### Chức năng quản lý cần bổ sung
- Quản lý lịch sử thay đổi phòng ban
- Theo dõi các phòng ban đang hoạt động / ngừng hoạt động
- HR thêm/sửa/khóa phòng ban phải gửi yêu cầu Admin duyệt trước khi public

---

## Module 4. Quản lý chức vụ

### Chức năng cần làm
- Thêm mới chức vụ
- Sửa chức vụ
- Khóa chức vụ
- Xem danh sách chức vụ

### Thông tin cần quản lý
- Tên chức vụ
- Mô tả
- Trạng thái chức vụ

### Chức năng quản lý cần bổ sung
- Quản lý lịch sử thay đổi chức vụ
- Theo dõi số nhân sự đang giữ từng chức vụ

---

## Module 5. Quản lý địa chỉ hành chính

### Chức năng cần làm
- Chọn tỉnh / thành phố
- Chọn xã / phường theo tỉnh / thành phố
- Lưu địa chỉ nhân sự

### Chức năng quản lý cần bổ sung
- Chuẩn hóa dữ liệu địa chỉ
- Hạn chế nhập địa chỉ sai hoặc trùng logic
---

## Module 6. Quản lý chấm công

### Chức năng cần làm
- Nhân viên check-in hàng ngày
- Nhân viên check-out hàng ngày
- Lưu thời gian check-in
- Lưu thời gian check-out
- Gửi thông báo cho HR khi nhân viên check-in
- Gửi thông báo cho HR khi nhân viên check-out
- HR xác nhận ngày công
- Xem báo cáo chấm công tháng hiện tại
- Xem báo cáo công quá khứ
- Xuất báo cáo chấm công Excel
- Xuất báo cáo chấm công PDF
### Quyền xem
- Admin xem toàn bộ
- HR xem toàn bộ
- Nhân viên chỉ xem công của mình

### Chức năng quản lý cần bổ sung
- Quản lý lịch sử duyệt chấm công
- Quản lý trạng thái công:
  - chờ duyệt
  - đã duyệt
  - từ chối
- Quản lý công theo tháng
- Quản lý thống kê đi muộn / về sớm nếu cần mở rộng
- Quản lý thao tác nhanh check-in / check-out tại dashboard

---

## Module 7. Quản lý dự án

### Chức năng cần làm
- Thêm mới dự án
- Sửa dự án
- Khóa dự án
- Xem danh sách dự án
- Lọc dự án theo trạng thái
- Xem chi tiết dự án

### Thông tin cần quản lý
- Tên dự án
- Ngày bắt đầu
- Trạng thái:
  - Kế hoạch
  - Đang triển khai
  - Tạm dừng
  - Hoàn thành
- Mô tả dự án
- Danh sách nhân sự tham gia
- Vai trò của từng nhân sự trong dự án

### Chức năng quản lý cần bổ sung
- Quản lý thành viên dự án
- Thêm nhân sự vào dự án
- Cập nhật vai trò nhân sự trong dự án
- Loại nhân sự khỏi dự án
- Xem danh sách dự án theo từng nhân sự
- Xem danh sách nhân sự theo từng dự án
- Theo dõi trạng thái dự án theo thời gian

---

## Module 8. Quản lý chi tiết triển khai dự án

### Chức năng cần làm
- Thêm chi tiết triển khai cho dự án
- Sửa chi tiết triển khai
- Xóa hoặc khóa chi tiết triển khai nếu cần
- Giao nhân sự thực hiện
- Chọn ngày thực hiện
- Nhập số ngày thực hiện
- Tự động tính ngày hoàn thành dự kiến

### Thông tin cần quản lý
- Nội dung công việc
- Nhân sự thực hiện
- Ngày thực hiện
- Thời gian thực hiện
- Ngày hoàn thành dự kiến
- Trạng thái đầu việc

### Chức năng quản lý cần bổ sung
- Quản lý tiến độ chi tiết của từng đầu việc
- Quản lý trạng thái đầu việc:
  - chưa làm
  - đang làm
  - hoàn thành
  - tạm dừng
- Quản lý lịch sử cập nhật đầu việc
- Nhân viên chỉ được xem đầu việc được giao
- Admin có quyền chỉnh sửa thời gian thực hiện

---

## Module 9. Quản lý tiến độ dự án

### Chức năng cần làm
- Tính tỷ lệ hoàn thành dự án
- Hiển thị phần trăm tiến độ của dự án đang triển khai
- Cập nhật tiến độ khi thay đổi chi tiết triển khai

### Logic nghiệp vụ
- Nếu chưa có chi tiết triển khai thì tiến độ = 0%
- Tiến độ dự án được tính theo tỷ trọng số ngày của từng chi tiết triển khai trong tổng thời gian dự án
- Khi đầu việc hoàn thành thì cộng phần trăm tương ứng vào tiến độ dự án

### Chức năng quản lý cần bổ sung
- Quản lý lịch sử thay đổi tiến độ
- Theo dõi tiến độ theo thời gian
- Thống kê các đầu việc hoàn thành / chưa hoàn thành
- Cảnh báo dự án chậm tiến độ nếu cần mở rộng

---

## Module 10. Quản lý duyệt thay đổi

### Chức năng cần làm
- HR gửi yêu cầu thay đổi lương cơ bản
- Admin duyệt hoặc từ chối thay đổi lương
- HR gửi yêu cầu thêm / sửa / khóa phòng ban
- Admin duyệt hoặc từ chối thay đổi phòng ban

### Chức năng quản lý cần bổ sung
- Quản lý danh sách yêu cầu chờ duyệt
- Quản lý lịch sử duyệt
- Xem nội dung thay đổi trước và sau
- Gửi thông báo sau khi duyệt / từ chối

---

## Module 11. Quản lý báo cáo và tổng quan

### Chức năng cần làm
- Hiển thị tổng số nhân sự
- Hiển thị tổng số dự án
- Hiển thị số lượng dự án theo trạng thái
- Hiển thị tỷ lệ hoàn thành của từng dự án đang triển khai
- Hiển thị báo cáo chấm công tháng hiện tại
- Có thao tác nhanh check-in / check-out

### Chức năng quản lý cần bổ sung
- Báo cáo nhân sự theo phòng ban
- Báo cáo dự án theo trạng thái
- Báo cáo tiến độ dự án
- Báo cáo chấm công tháng
- Xuất báo cáo Excel / PDF
- Báo cáo theo quyền:
  - Admin xem tất cả
  - HR xem dữ liệu nhân sự và chấm công
  - Nhân viên xem dữ liệu cá nhân

---

## Module 12. Quản lý phản hồi và trao đổi

### Chức năng cần làm
- Nhân viên gửi phản hồi / đề xuất tới HR
- Nhân viên gửi phản hồi / đề xuất tới Admin
- Theo dõi trạng thái phản hồi
- Trả lời phản hồi

### Chức năng quản lý cần bổ sung
- Quản lý danh sách phản hồi
- Quản lý phản hồi chưa xử lý / đã xử lý
- Quản lý lịch sử trả lời
- Nếu có email thật thì quản lý log gửi mail

---

## Module 13. Quản lý thông báo

### Chức năng cần làm
- Tạo thông báo hệ thống
- Gửi thông báo tới đúng người dùng
- Thông báo khi nhân viên check-in
- Thông báo khi nhân viên check-out
- Thông báo khi có yêu cầu duyệt
- Thông báo khi duyệt xong

### Chức năng quản lý cần bổ sung
- Xem danh sách thông báo
- Đánh dấu đã đọc / chưa đọc
- Quản lý thông báo theo vai trò
- Quản lý thông báo realtime nếu hệ thống hỗ trợ

---

## Module 14. Quản lý cấu hình website

### Chức năng cần làm
- Quản lý logo website
- Quản lý favicon
- Quản lý header
- Quản lý footer
- Quản lý nội dung footer
- Quản lý thông tin liên hệ website

### Quyền
- Chỉ Admin được thao tác

---

## Module 15. Quản lý nhật ký hệ thống

### Chức năng cần làm
- Ghi nhận ai thêm / sửa / khóa dữ liệu
- Ghi nhận ai duyệt / từ chối yêu cầu
- Ghi nhận thời gian thao tác
- Ghi nhận lịch sử thay đổi dữ liệu quan trọng

### Chức năng quản lý cần bổ sung
- Xem nhật ký hệ thống
- Lọc theo người thao tác
- Lọc theo module
- Lọc theo thời gian

---

## 4. Các màn hình cần xây dựng

### 4.1. Màn hình chung
- Đăng nhập
- Trang tổng quan
- Hồ sơ cá nhân
- Đổi mật khẩu
- Danh sách thông báo

### 4.2. Màn hình quản lý nhân sự
- Danh sách nhân sự
- Form thêm mới nhân sự
- Form sửa nhân sự
- Trang chi tiết nhân sự

### 4.3. Màn hình phòng ban và chức vụ
- Danh sách phòng ban
- Form thêm / sửa phòng ban
- Danh sách chức vụ
- Form thêm / sửa chức vụ

### 4.4. Màn hình chấm công
- Nút check-in
- Nút check-out
- Danh sách chấm công
- Báo cáo chấm công tháng
- Trang duyệt công cho HR

### 4.5. Màn hình dự án
- Danh sách dự án
- Form thêm / sửa dự án
- Trang chi tiết dự án
- Danh sách thành viên dự án
- Danh sách chi tiết triển khai

### 4.6. Màn hình duyệt thay đổi
- Danh sách yêu cầu chờ duyệt
- Trang chi tiết yêu cầu duyệt
- Nút duyệt / từ chối

### 4.7. Màn hình phản hồi
- Danh sách phản hồi
- Form gửi phản hồi
- Trang trả lời phản hồi

### 4.8. Màn hình cấu hình website
- Cấu hình logo
- Cấu hình favicon
- Cấu hình header / footer

### 4.9. Màn hình nhật ký hệ thống
- Danh sách log thao tác
- Bộ lọc log

---

## 5. Thứ tự ưu tiên triển khai

### Giai đoạn 1
- Đăng nhập
- Phân quyền
- Layout và menu hệ thống

### Giai đoạn 2
- Quản lý phòng ban
- Quản lý chức vụ
- Quản lý địa chỉ hành chính

### Giai đoạn 3
- Quản lý nhân sự
- Validate dữ liệu nhân sự
- Khóa nhân sự

### Giai đoạn 4
- Chấm công
- Duyệt chấm công
- Báo cáo chấm công

### Giai đoạn 5
- Quản lý dự án
- Thành viên dự án
- Chi tiết triển khai
- Tính tiến độ dự án

### Giai đoạn 6
- Duyệt thay đổi
- Thông báo
- Phản hồi
- Cấu hình website

### Giai đoạn 7
- Dashboard báo cáo
- Xuất Excel / PDF
- Nhật ký hệ thống

---

## 6. Kết luận
Dự án HRM này cần được triển khai theo hướng quản lý tổng thể nhân sự, dự án và chấm công trong doanh nghiệp. Hệ thống không chỉ dừng ở các chức năng CRUD cơ bản mà còn cần có các chức năng quản lý nâng cao như duyệt thay đổi, lịch sử thao tác, báo cáo, thông báo và cấu hình hệ thống. Đây là các chức năng cần thiết để hệ thống có thể vận hành thực tế trong môi trường doanh nghiệp.
