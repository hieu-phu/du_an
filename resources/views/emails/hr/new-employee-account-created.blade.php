<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản nhân viên mới</title>
</head>
<body style="margin:0; padding:24px 0; background:#f3f6fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    @php
        $appName = config('app.name', 'HRM');
        $detailUrl = url('/users/employees?detail_user=' . $createdUser->id);
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                                <tr>
                                    <td style="padding:28px 32px; background:linear-gradient(135deg, #0f766e 0%, #0f9b8e 100%); color:#ffffff;">
                                        <div style="font-size:13px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.9;">{{ $appName }}</div>
                                        <div style="margin-top:10px; font-size:28px; font-weight:700; line-height:1.3;">Tài khoản nhân viên mới</div>
                                        <div style="margin-top:8px; font-size:15px; opacity:0.92;">Hệ thống vừa ghi nhận một tài khoản nhân viên được tạo mới.</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:28px 32px 12px;">
                                        <p style="margin:0 0 12px; font-size:16px;">Xin chào <strong>{{ $admin->name ?: $admin->email }}</strong>,</p>
                                        <p style="margin:0; font-size:15px; line-height:1.7; color:#475569;">
                                            <strong>{{ $actor->name }}</strong> vừa tạo tài khoản nhân viên mới trên hệ thống.
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 32px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate; border-spacing:0; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px;">
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b; width:180px;">Họ tên nhân viên</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $createdUser->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Email</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $createdUser->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Số điện thoại</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $createdUser->phone ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; font-size:14px; color:#64748b;">Người tạo</td>
                                                <td style="padding:18px 20px; font-size:15px; font-weight:600; color:#0f172a;">{{ $actor->name }} ({{ $actor->email }})</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 32px 0;">
                                        <a href="{{ $detailUrl }}" style="display:inline-block; padding:12px 18px; border-radius:12px; background:#0f766e; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
                                            Xem chi tiết nhân viên
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:24px 32px 32px; font-size:13px; line-height:1.7; color:#64748b;">
                                        Email này được gửi tự động từ hệ thống <strong>{{ $appName }}</strong>.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
