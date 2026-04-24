$path = "e:\CODE\DEV\du_an\app\Services\AttendanceService.php"
# Đọc file dưới dạng byte để đảm bảo không bị lỗi encoding khi đọc
$bytes = [System.IO.File]::ReadAllBytes($path)
$content = [System.Text.Encoding]::UTF8.GetString($bytes)

# Loại bỏ tất cả ký tự trắng, BOM, hoặc bất kỳ thứ gì ở đầu cho đến khi gặp <?php
$startIndex = $content.IndexOf("<?php")
if ($startIndex -ge 0) {
    $cleanContent = $content.Substring($startIndex)
    # Ghi lại file với định dạng UTF8 chuẩn (không có BOM)
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($path, $cleanContent, $utf8NoBom)
    Write-Host "Success: Stripped leading characters and saved as UTF-8 (No BOM)."
} else {
    Write-Host "Error: Could not find <?php in the file."
}
