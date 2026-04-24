$path = "e:\CODE\DEV\du_an\app\Services\AttendanceService.php"
$bytes = [System.IO.File]::ReadAllBytes($path)
$hex = [System.BitConverter]::ToString($bytes[0..10])
Write-Host "Original first 10 bytes: $hex"

# Tìm vị trí chuỗi "<?php" (3C 3F 70 68 70)
$index = -1
for ($i = 0; $i -lt ($bytes.Length - 5); $i++) {
    if ($bytes[$i] -eq 0x3C -and $bytes[$i+1] -eq 0x3F -and $bytes[$i+2] -eq 0x70 -and $bytes[$i+3] -eq 0x68 -and $bytes[$i+4] -eq 0x70) {
        $index = $i
        break
    }
}

if ($index -gt 0) {
    Write-Host "Found <?php at index $index. Stripping leading bytes..."
    $newBytes = $bytes[$index..($bytes.Length - 1)]
    [System.IO.File]::WriteAllBytes($path, $newBytes)
    Write-Host "File fixed successfully."
} elseif ($index -eq 0) {
    Write-Host "<?php is already at the beginning. No changes needed."
} else {
    Write-Host "<?php not found in the file."
}
