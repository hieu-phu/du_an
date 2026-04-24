import os

file_path = r'e:\CODE\DEV\du_an\app\Services\AttendanceService.php'

with open(file_path, 'rb') as f:
    content = f.read()

# Kiểm tra xem có BOM hoặc khoảng trắng trước <?php không
# UTF-8 BOM là b'\xef\xbb\xbf'
print(f"Original first 20 bytes: {content[:20]}")

# Tìm vị trí của <?php
php_start = content.find(b'<?php')
if php_start > 0:
    print(f"Found <?php at position {php_start}, stripping leading bytes.")
    new_content = content[php_start:]
    with open(file_path, 'wb') as f:
        f.write(new_content)
    print("File fixed successfully.")
elif php_start == 0:
    print("<?php is already at the beginning of the file.")
else:
    print("<?php not found in the file!")
