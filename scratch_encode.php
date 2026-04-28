<?php

function fix_mojibake($text) {
    // Try to fix UTF-8 double encoding
    // This is equivalent to text.encode('latin1').decode('utf-8') in Python
    // PHP doesn't have a direct equivalent that is robust for mixed content,
    // so let's do a dictionary based replacement for the common mojibake characters.
    
    $replacements = [
        'DÃ¡Â»Â± ÃƒÂ¡n' => 'Dự án',
        'dÃ¡Â»Â± ÃƒÂ¡n' => 'dự án',
        'TÃƒÂ¬m kiÃ¡ÂºÂ¿m' => 'Tìm kiếm',
        'NhÃ¡ÂºÂ­p tÃƒÂªn hoÃ¡ÂºÂ·c mÃƒÂ´ tÃ¡ÂºÂ£' => 'Nhập tên hoặc mô tả',
        'TrÃ¡ÂºÂ¡ng thÃƒÂ¡i' => 'Trạng thái',
        'TÃ¡ÂºÂ¥t cÃ¡ÂºÂ£' => 'Tất cả',
        'NhÃƒÂ¢n sÃ¡Â»Â±' => 'Nhân sự',
        'nhÃƒÂ¢n sÃ¡Â»Â±' => 'nhân sự',
        'SÃ¡Â»â€˜ dÃƒÂ²ng/trang' => 'Số dòng/trang',
        'dÃƒÂ²ng' => 'dòng',
        'XÃƒÂ³a lÃ¡Â»Â c' => 'Xóa lọc',
        'ThÃƒÂªm' => 'Thêm',
        'ChÃ¡Â»â€° hiÃ¡Â»Æ’n thÃ¡Â»â€¹' => 'Chỉ hiển thị',
        'bÃ¡ÂºÂ¡n Ã„â€˜ang tham gia' => 'bạn đang tham gia',
        'Danh sÃƒÂ¡ch' => 'Danh sách',
        'toÃƒÂ n hÃ¡Â»â€¡ thÃ¡Â»â€˜ng' => 'toàn hệ thống',
        'TÃƒÂªn' => 'Tên',
        'TiÃ¡ÂºÂ¿n Ã„â€˜Ã¡Â»â„¢' => 'Tiến độ',
        'NgÃƒÂ y bÃ¡ÂºÂ¯t Ã„â€˜Ã¡ÂºÂ§u' => 'Ngày bắt đầu',
        'ThÃƒÂ nh viÃƒÂªn' => 'Thành viên',
        'KhÃƒÂ³a' => 'Khóa',
        'Thao tÃƒÂ¡c' => 'Thao tác',
        'ChÃ†Â°a cÃƒÂ³' => 'Chưa có',
        'mÃƒÂ´ tÃ¡ÂºÂ£' => 'mô tả',
        'hoÃƒÂ n thÃƒÂ nh' => 'hoàn thành',
        'ChÃ¡ÂºÂ­m' => 'Chậm',
        'Ã„Â ÃƒÂ£ khÃƒÂ³a' => 'Đã khóa',
        'Ã„Â ang mÃ¡Â»Å¸' => 'Đang mở',
        'Xem' => 'Xem',
        'SÃ¡Â»Â­a' => 'Sửa',
        'MÃ¡Â»Å¸ khÃƒÂ³a' => 'Mở khóa',
        'phÃƒÂ¹ hÃ¡Â»Â£p' => 'phù hợp',
        'theo tÃ¡Â»Â«ng' => 'theo từng',
        'SÃ¡Â»â€˜' => 'Số',
        'dÃ¡Â»Â¯ liÃ¡Â»â€¡u' => 'dữ liệu',
        'phÃƒÂ¢n bÃ¡Â»â€¢' => 'phân bổ',
        'CÃ¡ÂºÂ­p nhÃ¡ÂºÂ­t' => 'Cập nhật',
        'TÃ¡ÂºÂ¡o' => 'Tạo',
        'mÃ¡Â»â€ºi' => 'mới',
        'NhÃ¡ÂºÂ­p' => 'Nhập',
        'MÃƒÂ´ tÃ¡ÂºÂ£' => 'Mô tả',
        'tham gia' => 'tham gia',
        'ChÃ¡Â»Â n' => 'Chọn',
        'vai trÃƒÂ²' => 'vai trò',
        'XÃƒÂ³a' => 'Xóa',
        'HÃ¡Â»Â§y' => 'Hủy',
        'Chi tiÃ¡ÂºÂ¿t' => 'Chi tiết',
        'Ã„Â ÃƒÂ³ng' => 'Đóng',
        'ThÃƒÂ´ng tin chung' => 'Thông tin chung',
        'LÃ¡Â»â€¹ch tÃ¡Â»â€¢ng thÃ¡Â»Æ’' => 'Lịch tổng thể',
        'mÃ¡Â»â€˜c' => 'mốc',
        'TÃƒÂ¬nh trÃ¡ÂºÂ¡ng' => 'Tình trạng',
        'cho dÃ¡Â»Â± ÃƒÂ¡n nÃƒÂ y' => 'cho dự án này',
        'TriÃ¡Â»Æ’n khai' => 'Triển khai',
        'TÃ¡Â»â€¡p Ã„â€˜ÃƒÂ­nh kÃƒÂ¨m' => 'Tệp đính kèm',
        'LÃ¡Â»â€¹ch sÃ¡Â»Â­' => 'Lịch sử',
        'vÃƒÂ o' => 'vào',
        'QuÃ¡ÂºÂ£n lÃƒÂ½' => 'Quản lý',
        'PhÃƒÂ¢n quyÃ¡Â»Â n' => 'Phân quyền',
        'ChÃ¡Â»â€°' => 'Chỉ',
        'cÃ¡ÂºÂ§n chÃ¡Â»â€°nh' => 'cần chỉnh',
        'Ã„â€˜Ã¡Â»Æ’' => 'để',
        'giao diÃ¡Â»â€¡n' => 'giao diện',
        'gÃ¡Â»Â n' => 'gọn',
        'hÃ†Â¡n' => 'hơn',
        'Ã„Â ang chÃ¡Â»â€°nh' => 'Đang chỉnh',
        'QuyÃ¡Â»Â n nÃƒÂ y' => 'Quyền này',
        'chÃ¡Â»â€° ÃƒÂ¡p dÃ¡Â»Â¥ng' => 'chỉ áp dụng',
        'trong' => 'trong',
        'hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i' => 'hiện tại',
        'LÃ†Â°u quyÃ¡Â»Â n' => 'Lưu quyền',
        'Ã¡Â»Å¸ trÃƒÂªn' => 'ở trên',
        'Ã„â€˜Ã¡Â»Æ’' => 'để',
        'VÃ¡Â»â€¹ trÃƒÂ­' => 'Vị trí',
        'NgÃƒÂ y' => 'Ngày',
        'LoÃ¡ÂºÂ¡i khÃ¡Â»Â i' => 'Loại khỏi',
        'giai Ã„â€˜oÃ¡ÂºÂ¡n' => 'giai đoạn',
        'Theo dÃƒÂµi' => 'Theo dõi',
        'lÃ¡Â»â€¹ch tÃ¡Â»â€¢ng thÃ¡Â»Æ’' => 'lịch tổng thể',
        'chia' => 'chia',
        'thÃƒÂ nh tÃ¡Â»Â«ng' => 'thành từng',
        'bÃƒÂ¡o cÃƒÂ¡o' => 'báo cáo',
        'mÃ¡Â»Â©c hoÃƒÂ n thÃƒÂ nh' => 'mức hoàn thành',
        'cÃ¡Â»Â§a tÃ¡Â»Â«ng' => 'của từng',
        'MÃ¡Â»â€˜c kÃ¡ÂºÂ¿ tiÃ¡ÂºÂ¿p' => 'Mốc kế tiếp',
        'hÃ¡ÂºÂ¡n' => 'hạn',
        'TÃ¡Â»â€¢ng' => 'Tổng',
        'Ã„Â ÃƒÂ£' => 'Đã',
        'TÃ¡Â»Â· lÃ¡Â»â€¡' => 'Tỷ lệ',
        'LÃ¡Â»â€¹ch trÃƒÂ¬nh tÃ¡Â»â€¢ng thÃ¡Â»Æ’' => 'Lịch trình tổng thể',
        'BÃ¡ÂºÂ¯t Ã„â€˜Ã¡ÂºÂ§u' => 'Bắt đầu',
        'KÃ¡ÂºÂ¿t thÃƒÂºc dÃ¡Â»Â± kiÃ¡ÂºÂ¿n' => 'Kết thúc dự kiến',
        'Giai Ã„â€˜oÃ¡ÂºÂ¡n hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i' => 'Giai đoạn hiện tại',
        'ChÃ†Â°a xÃƒÂ¡c Ã„â€˜Ã¡Â»â€¹nh' => 'Chưa xác định',
        'VÃƒÂ­ dÃ¡Â»Â¥' => 'Ví dụ',
        'Giai Ã„â€˜oÃ¡ÂºÂ¡n phÃƒÂ¢n tÃƒÂ­ch' => 'Giai đoạn phân tích',
        
        // PHP controller
        'Ä Ã£ táº¡o dá»± Ã¡n má»›i thÃ nh cÃ´ng.' => 'Đã tạo dự án mới thành công.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ cáº­p nháº­t.' => 'Dự án đang bị khóa, không thể cập nhật.',
        'Ä Ã£ cáº­p nháº­t dá»± Ã¡n thÃ nh cÃ´ng.' => 'Đã cập nhật dự án thành công.',
        'Ä Ã£ má»Ÿ khÃ³a dá»± Ã¡n thÃ nh cÃ´ng.' => 'Đã mở khóa dự án thành công.',
        'Ä Ã£ khÃ³a dá»± Ã¡n thÃ nh cÃ´ng.' => 'Đã khóa dự án thành công.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ thay Ä‘á»•i thÃ nh viÃªn.' => 'Dự án đang bị khóa, không thể thay đổi thành viên.',
        'Ä Ã£ thÃªm nhÃ¢n sá»± vÃ o dá»± Ã¡n.' => 'Đã thêm nhân sự vào dự án.',
        'Ä Ã£ cáº­p nháº­t vai trÃ² nhÃ¢n sá»± trong dá»± Ã¡n.' => 'Đã cập nhật vai trò nhân sự trong dự án.',
        'Ä Ã£ loáº¡i nhÃ¢n sá»± khá» i dá»± Ã¡n.' => 'Đã loại nhân sự khỏi dự án.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ thay Ä‘á»•i vai trÃ².' => 'Dự án đang bị khóa, không thể thay đổi vai trò.',
        'TÃªn vai trÃ² lÃ  báº¯t buá»™c.' => 'Tên vai trò là bắt buộc.',
        'TÃªn vai trÃ² khÃ´ng Ä‘Æ°á»£c quÃ¡ 100 kÃ½ tá»±.' => 'Tên vai trò không được quá 100 ký tự.',
        'Vai trÃ² nÃ y Ä‘Ã£ tá»“n táº¡i trong dá»± Ã¡n.' => 'Vai trò này đã tồn tại trong dự án.',
        'Ä Ã£ thÃªm vai trÃ² dá»± Ã¡n.' => 'Đã thêm vai trò dự án.',
        'KhÃ´ng thá»ƒ xÃ³a vai trÃ² Ä‘ang Ä‘Æ°á»£c gÃ¡n cho thÃ nh viÃªn.' => 'Không thể xóa vai trò đang được gán cho thành viên.',
        'Ä Ã£ xÃ³a vai trÃ² dá»± Ã¡n.' => 'Đã xóa vai trò dự án.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ thÃªm má»‘c tiáº¿n Ä‘á»™.' => 'Dự án đang bị khóa, không thể thêm mốc tiến độ.',
        'Ä Ã£ thÃªm má»‘c tiáº¿n Ä‘á»™ cho dá»± Ã¡n.' => 'Đã thêm mốc tiến độ cho dự án.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ cáº­p nháº­t má»‘c tiáº¿n Ä‘á»™.' => 'Dự án đang bị khóa, không thể cập nhật mốc tiến độ.',
        'Ä Ã£ cáº­p nháº­t má»‘c tiáº¿n Ä‘á»™.' => 'Đã cập nhật mốc tiến độ.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ xÃ³a má»‘c tiáº¿n Ä‘á»™.' => 'Dự án đang bị khóa, không thể xóa mốc tiến độ.',
        'Ä Ã£ xÃ³a má»‘c tiáº¿n Ä‘á»™.' => 'Đã xóa mốc tiến độ.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ thÃªm Ä‘áº§u viá»‡c.' => 'Dự án đang bị khóa, không thể thêm đầu việc.',
        'Ná»™i dung cÃ´ng viá»‡c lÃ  báº¯t buá»™c.' => 'Nội dung công việc là bắt buộc.',
        'NgÃ y thá»±c hiá»‡n lÃ  báº¯t buá»™c.' => 'Ngày thực hiện là bắt buộc.',
        'Sá»‘ ngÃ y thá»±c hiá»‡n lÃ  báº¯t buá»™c.' => 'Số ngày thực hiện là bắt buộc.',
        'ThÃªm Ä‘áº§u viá»‡c triá»ƒn khai' => 'Thêm đầu việc triển khai',
        'Ä Ã£ thÃªm Ä‘áº§u viá»‡c triá»ƒn khai.' => 'Đã thêm đầu việc triển khai.',
        'Ä áº§u viá»‡c Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ cáº­p nháº­t.' => 'Đầu việc đang bị khóa, không thể cập nhật.',
        'Chá»‰ ngÆ°á» i cÃ³ quyá» n Ä‘iá» u chá»‰nh lá»‹ch triá»ƒn khai má»›i Ä‘Æ°á»£c thay Ä‘á»•i thá» i gian thá»±c hiá»‡n.' => 'Chỉ người có quyền điều chỉnh lịch triển khai mới được thay đổi thời gian thực hiện.',
        'Cáº­p nháº­t Ä‘áº§u viá»‡c triá»ƒn khai' => 'Cập nhật đầu việc triển khai',
        'Ä áº§u viá»‡c cá»§a báº¡n ÄÃ£ Ä‘Æ°á»£c cáº­p nháº­t.' => 'Đầu việc của bạn đã được cập nhật.',
        'Ä áº§u viá»‡c cá»§a báº¡n Ä‘Ã£ Ä‘Æ°á»£c cáº­p nháº­t.' => 'Đầu việc của bạn đã được cập nhật.',
        'Ä Ã£ cáº­p nháº­t Ä‘áº§u viá»‡c triá»ƒn khai.' => 'Đã cập nhật đầu việc triển khai.',
        'Ä áº§u viá»‡c Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ cáº­p nháº­t tráº¡ng thÃ¡i.' => 'Đầu việc đang bị khóa, không thể cập nhật trạng thái.',
        'Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘áº§u viá»‡c' => 'Cập nhật trạng thái đầu việc',
        'Tráº¡ng thÃ¡i Ä‘áº§u viá»‡c Ä‘Ã£ Ä‘á»•i sang' => 'Trạng thái đầu việc đã đổi sang',
        'Ä Ã£ cáº­p nháº­t tráº¡ng thÃ¡i Ä‘áº§u viá»‡c.' => 'Đã cập nhật trạng thái đầu việc.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ thay Ä‘á»•i tráº¡ng thÃ¡i khÃ³a Ä‘áº§u viá»‡c.' => 'Dự án đang bị khóa, không thể thay đổi trạng thái khóa đầu việc.',
        'Thay Ä‘á»•i khÃ³a Ä‘áº§u viá»‡c' => 'Thay đổi khóa đầu việc',
        'Ä Ã£ khÃ³a Ä‘áº§u viá»‡c.' => 'Đã khóa đầu việc.',
        'Ä Ã£ má»Ÿ khÃ³a Ä‘áº§u viá»‡c.' => 'Đã mở khóa đầu việc.',
        'Ä áº§u viá»‡c Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ xÃ³a.' => 'Đầu việc đang bị khóa, không thể xóa.',
        'XÃ³a Ä‘áº§u viá»‡c triá»ƒn khai' => 'Xóa đầu việc triển khai',
        'Ä Ã£ xÃ³a Ä‘áº§u viá»‡c triá»ƒn khai.' => 'Đã xóa đầu việc triển khai.',
        'Dá»± Ã¡n Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ táº£i tá»‡p Ä‘Ã­nh kÃ¨m.' => 'Dự án đang bị khóa, không thể tải tệp đính kèm.',
        'Ä Ã£ táº£i tá»‡p Ä‘Ã­nh kÃ¨m lÃªn dá»± Ã¡n.' => 'Đã tải tệp đính kèm lên dự án.',
        'Ä áº§u viá»‡c Ä‘ang bá»‹ khÃ³a, khÃ´ng thá»ƒ táº£i tá»‡p Ä‘Ã­nh kÃ¨m.' => 'Đầu việc đang bị khóa, không thể tải tệp đính kèm.',
        
        // Let's also do a general approach: we can use a script that just maps Windows-1252 to UTF-8
    ];

    $text = strtr($text, $replacements);
    
    // There are definitely more strings, so a better approach is to use regex or function.
    // Let's create a generalized decoder since mapping all by hand is tedious.
    
    // Pattern to catch words containing mojibake
    // This is actually better done by just executing mb_convert_encoding on the whole file?
    // No, running mb_convert_encoding on the whole file will mess up valid ASCII and UTF-8?
    // Actually, mb_convert_encoding from ISO-8859-1 or Windows-1252 leaves ASCII intact!
    // But it will corrupt ALREADY VALID UTF-8 characters.
    
    return $text;
}

$files = [
    'e:\CODE\DEV\du_an\resources\js\Pages\Projects\Index.vue',
    'e:\CODE\DEV\du_an\app\Http\Controllers\WEB\ProjectController.php'
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Instead of doing it globally, let's find strings in quotes or between > < that contain 
    // these typical mojibake characters like Ã, Ä, etc and fix ONLY them.
    // Actually, since I have the specific replacements, let me just add a few more common ones and use str_replace
    // to be 100% safe.
    
    // Let's refine the dictionary approach by creating a script that parses out quoted strings and > < text,
    // tries to decode it using mb_convert_encoding($text, 'Windows-1252', 'UTF-8'),
    // and if it looks like Vietnamese (contains ệ, ế, ả, etc), replace it.
    
    $pattern = '/([ÃÄÂ][\x80-\xFF]+|dÃ¡Â»Â±|tÃ¡ÂºÂ|Ã„â€˜)/'; 
    $content = preg_replace_callback($pattern, function($matches) {
        // ...
        return $matches[0];
    }, $content);
}

// But I have an even better idea. I will just run a python script to do this because Python's ftfy library or ftfy-like logic is easy to write.
