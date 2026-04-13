# Chức năng cần làm cho dự án HRM

## 1. Mục tiêu dự án
Xây dựng hệ thống HRM giúp doanh nghiệp quản lý nhân sự, phòng ban, chức vụ, chấm công, dự án, báo cáo tổng quan và phân quyền theo từng vai trò sử dụng.

## 2. Các vai trò trong hệ thống
- Admin
- HR
- Nhân viên

---

## 3. Các module chức năng chính

### Module 1. Tài khoản và phân quyền
#### Chức năng cần làm
- Đăng nhập hệ thống
- Quản lý tài khoản người dùng
- Khóa hoặc mở tài khoản
- Phân quyền theo vai trò: Admin, HR, Nhân viên
- Kiểm tra quyền truy cập theo từng màn hình và từng chức năng
- Hiển thị menu theo quyền của từng vai trò

#### Yêu cầu nghiệp vụ
- Admin có toàn quyền
- HR chỉ thao tác được các chức năng liên quan nhân sự, chấm công, phòng ban
- Nhân viên chỉ xem dữ liệu của bản thân và các dự án mình tham gia

---

### Module 2. Quản lý nhân sự
#### Chức năng cần làm
- Thêm mới nhân sự
- Sửa thông tin nhân sự
- Khóa nhân sự
- Hiển thị danh sách nhân sự
- Xem chi tiết hồ sơ nhân sự
- Lọc danh sách nhân sự theo:
  - tên nhân sự
  - thời gian bắt đầu làm việc
  - phòng ban
- Xuất danh sách nhân sự ra Excel
- Xuất danh sách nhân sự ra PDF

#### Dữ liệu cần quản lý
- Tên nhân sự
- Email
- Điện thoại
- Địa chỉ
- Ngày bắt đầu làm việc
- Ngày sinh
- Lương cơ bản
- Phòng ban
- Chức vụ
- Avatar Gmail
- Trạng thái online hoặc offline

#### Validate cần xử lý
- Tên nhân sự bắt buộc nhập
- Email bắt buộc là Gmail
- Kiểm tra trùng email với cơ sở dữ liệu
- Điện thoại đúng định dạng
- Ngày sinh hợp lệ
- Ngày bắt đầu làm việc hợp lệ
- Lương cơ bản là số hợp lệ

---

### Module 3. Quản lý phòng ban
#### Chức năng cần làm
- Thêm mới phòng ban
- Sửa phòng ban
- Khóa phòng ban
- Hiển thị danh sách phòng ban
- Hiển thị số lượng nhân sự theo từng phòng ban

#### Dữ liệu cần quản lý
- Tên phòng ban
- Mô tả chức năng của phòng ban
- Trạng thái hoạt động

#### Validate cần xử lý
- Tên phòng ban bắt buộc nhập
- Tên phòng ban không trùng

#### Yêu cầu nghiệp vụ
- HR có thể tạo, sửa, khóa phòng ban nhưng phải chờ Admin duyệt trước khi public

---

### Module 4. Quản lý chức vụ
#### Chức năng cần làm
- Thêm mới chức vụ
- Sửa chức vụ
- Khóa chức vụ
- Hiển thị danh sách chức vụ

#### Dữ liệu cần quản lý
- Tên chức vụ
- Mô tả chức vụ
- Trạng thái hoạt động

#### Validate cần xử lý
- Tên chức vụ bắt buộc nhập
- Tên chức vụ không trùng

---

### Module 5. Chấm công
#### Chức năng cần làm
- Check-in hằng ngày
- Check-out hằng ngày
- Lưu lịch sử check-in và check-out
- Gửi thông báo cho HR khi nhân sự check-in
- Gửi thông báo cho HR khi nhân sự check-out
- HR xác nhận ngày công của nhân sự
- Hiển thị báo cáo chấm công theo tháng
- Xuất báo cáo chấm công ra Excel
- Xuất báo cáo chấm công ra PDF

#### Yêu cầu nghiệp vụ
- Admin và HR xem được chấm công của toàn bộ nhân sự
- Nhân viên chỉ xem được chấm công của chính mình
- Không áp dụng nút check-in hoặc check-out nhanh cho tài khoản Admin

#### Dữ liệu cần quản lý
- Ngày chấm công
- Thời gian check-in
- Thời gian check-out
- Trạng thái xác nhận công
- Tổng số công trong tháng

---

### Module 6. Quản lý dự án
#### Chức năng cần làm
- Thêm mới dự án
- Sửa thông tin dự án
- Khóa dự án
- Hiển thị danh sách dự án
- Xem chi tiết dự án
- Gán nhiều nhân sự tham gia dự án
- Gán vai trò cho từng nhân sự trong dự án

#### Dữ liệu cần quản lý
- Tên dự án
- Ngày bắt đầu
- Trạng thái dự án
- Mô tả dự án
- Danh sách nhân sự tham gia
- Vai trò của từng nhân sự trong dự án

#### Validate cần xử lý
- Tên dự án bắt buộc nhập
- Ngày bắt đầu hợp lệ
- Trạng thái dự án hợp lệ

#### Trạng thái dự án
- Kế hoạch
- Đang triển khai
- Tạm dừng
- Hoàn thành

#### Yêu cầu nghiệp vụ
- Mặc định trạng thái dự án là Đang triển khai
- Admin xem được toàn bộ dự án
- Nhân viên chỉ xem được các dự án mình tham gia

---

### Module 7. Quản lý chi tiết triển khai dự án
#### Chức năng cần làm
- Thêm chi tiết triển khai cho từng dự án
- Sửa chi tiết triển khai
- Xem danh sách chi tiết triển khai
- Giao chi tiết triển khai cho nhân sự thực hiện
- Tính ngày hoàn thành dự kiến
- Tính tỷ lệ hoàn thành dự án

#### Dữ liệu cần quản lý
- Nội dung công việc
- Nhân sự thực hiện
- Ngày thực hiện
- Thời gian thực hiện theo số ngày
- Ngày hoàn thành dự kiến
- Tỷ trọng của từng công việc trong dự án

#### Quy tắc tính toán
- Ngày hoàn thành dự kiến = ngày thực hiện + thời gian thực hiện
- Khi chưa có chi tiết triển khai thì tỷ lệ hoàn thành dự án = 0%
- Tỷ lệ hoàn thành được tính dựa trên tổng thời gian của toàn bộ chi tiết triển khai

#### Yêu cầu nghiệp vụ
- Admin có quyền chỉnh sửa thời gian thực hiện
- Nhân sự được giao chỉ có quyền xem, không được sửa nội dung này

---

### Module 8. Dashboard và báo cáo tổng quan
#### Chức năng cần làm
- Hiển thị tổng số nhân sự trong công ty
- Hiển thị tổng số dự án
- Hiển thị số lượng dự án theo trạng thái
- Hiển thị tỷ lệ hoàn thành của từng dự án đang triển khai
- Hiển thị báo cáo chấm công trong tháng hiện tại
- Có thao tác nhanh check-in và check-out

#### Yêu cầu nghiệp vụ
- Admin xem được toàn bộ dữ liệu tổng quan
- HR xem được dữ liệu tổng quan phục vụ nhân sự và chấm công
- Nhân viên chỉ xem được dữ liệu liên quan đến bản thân và dự án mình tham gia

---

### Module 9. Phê duyệt thay đổi
#### Chức năng cần làm
- HR gửi yêu cầu thay đổi lương cơ bản để Admin duyệt
- HR gửi yêu cầu thêm, sửa, khóa phòng ban để Admin duyệt
- Admin duyệt hoặc từ chối yêu cầu
- Lưu lịch sử duyệt

#### Yêu cầu nghiệp vụ
- Các thay đổi chưa được Admin duyệt thì chưa public ra hệ thống chính thức

---

### Module 10. Thông báo hệ thống
#### Chức năng cần làm
- Gửi thông báo cho HR khi nhân sự check-in
- Gửi thông báo cho HR khi nhân sự check-out
- Gửi thông báo khi có yêu cầu chờ duyệt
- Đánh dấu đã đọc thông báo
- Hiển thị danh sách thông báo

---

### Module 11. Phản hồi và liên hệ
#### Chức năng cần làm
- Nhân viên gửi phản hồi hoặc đề xuất tới HR và Admin
- Admin phản hồi lại nhân viên
- HR phản hồi lại nhân viên
- Lưu trạng thái phản hồi

#### Yêu cầu nghiệp vụ
- Admin có thể trao đổi với nhân sự khác qua email
- Nhân viên có thể gửi phản hồi và đề xuất tới HR hoặc Admin

---

### Module 12. Cấu hình giao diện website
#### Chức năng cần làm
- Cập nhật logo website
- Cập nhật favicon
- Chỉnh sửa nội dung header
- Chỉnh sửa nội dung footer
- Cập nhật thông tin footer

#### Yêu cầu nghiệp vụ
- Chỉ Admin mới được thao tác module này

---

## 4. Các màn hình cần xây dựng

### Màn hình dùng chung
- Trang đăng nhập
- Trang dashboard
- Trang thông báo
- Trang hồ sơ cá nhân

### Màn hình quản lý nhân sự
- Danh sách nhân sự
- Form thêm nhân sự
- Form sửa nhân sự
- Trang chi tiết nhân sự

### Màn hình quản lý phòng ban và chức vụ
- Danh sách phòng ban
- Form thêm và sửa phòng ban
- Danh sách chức vụ
- Form thêm và sửa chức vụ

### Màn hình chấm công
- Nút check-in
- Nút check-out
- Lịch sử chấm công cá nhân
- Báo cáo chấm công toàn công ty theo tháng

### Màn hình quản lý dự án
- Danh sách dự án
- Form thêm và sửa dự án
- Trang chi tiết dự án
- Danh sách thành viên dự án
- Danh sách chi tiết triển khai

### Màn hình báo cáo
- Báo cáo tổng quan
- Báo cáo chấm công
- Báo cáo dự án

### Màn hình phản hồi và cấu hình
- Danh sách phản hồi
- Chi tiết phản hồi
- Trang cấu hình website

---

## 5. Các chức năng theo từng vai trò

### Admin
- Quản lý nhân sự
- Quản lý dự án
- Quản lý phòng ban
- Quản lý chức vụ
- Xem toàn bộ báo cáo
- Duyệt thay đổi từ HR
- Cấu hình giao diện website
- Trao đổi qua email

### HR
- Thêm mới nhân sự
- Sửa và khóa nhân sự
- Đề xuất thay đổi lương cơ bản
- Thêm, sửa, khóa phòng ban
- Xem danh sách nhân sự
- Xem danh sách dự án
- Check-in và check-out
- Xem và tải báo cáo chấm công toàn bộ nhân sự

### Nhân viên
- Xem thông tin cá nhân
- Xem các dự án đã và đang tham gia
- Xem tổng số công của bản thân theo tháng
- Gửi phản hồi và đề xuất tới HR hoặc Admin
- Check-in và check-out

---

## 6. Các chức năng ưu tiên nên làm trước
1. Đăng nhập và phân quyền
2. Quản lý phòng ban và chức vụ
3. Quản lý nhân sự
4. Chấm công check-in và check-out
5. Quản lý dự án
6. Chi tiết triển khai dự án
7. Dashboard và báo cáo
8. Phê duyệt thay đổi
9. Thông báo hệ thống
10. Cấu hình website

---

## 7. Kết luận
Dự án HRM này cần tập trung triển khai các nhóm chức năng chính gồm quản lý tài khoản và phân quyền, quản lý nhân sự, phòng ban, chức vụ, chấm công, quản lý dự án, chi tiết triển khai dự án, dashboard báo cáo, phê duyệt thay đổi, thông báo hệ thống, phản hồi nội bộ và cấu hình giao diện website. Đây là bộ chức năng đủ để đáp ứng yêu cầu quản lý nhân sự và dự án trong doanh nghiệp theo mô tả đề bài.
