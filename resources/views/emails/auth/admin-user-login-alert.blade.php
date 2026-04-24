<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canh bao dang nhap tài khoản</title>
</head>
<body style="margin:0; padding:24px 0; background:#f3f6fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    @php
        $appName = config('app.name', 'HRM');
        $adminName = $admin->name ?: $admin->email;
        $userName = $loggedInUser->name ?: $loggedInUser->email;
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                                <tr>
                                    <td style="padding:28px 32px; background:linear-gradient(135deg, #0f766e 0%, #0f766e 100%); color:#ffffff;">
                                        <div style="font-size:13px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.9;">{{ $appName }}</div>
                                        <div style="margin-top:10px; font-size:28px; font-weight:700; line-height:1.3;">Canh bao dang nhap tài khoản</div>
                                        <div style="margin-top:8px; font-size:15px; opacity:0.92;">Hệ thống vua ghi nhan mot phien dang nhap thanh cong cua nguoi dung trong hệ thống.</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:28px 32px 12px;">
                                        <p style="margin:0 0 12px; font-size:16px;">Xin chao <strong>{{ $adminName }}</strong>,</p>
                                        <p style="margin:0; font-size:15px; line-height:1.7; color:#475569;">
                                            Tài khoản <strong>{{ $userName }}</strong> vua dang nhap thanh cong vao hệ thống. Duoi day la thong tin phien dang nhap de admin theo doi.
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 32px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate; border-spacing:0; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px;">
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b; width:180px;">Nguoi dang nhap</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $userName }} ({{ $loggedInUser->email }})</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Thời gian</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $loggedInAt }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Phuong thuc dang nhap</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $loginMethod }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Dia chi IP</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;">{{ $ipAddress }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; font-size:14px; color:#64748b; vertical-align:top;">Trinh duyệt / thiet bi</td>
                                                <td style="padding:18px 20px; font-size:15px; font-weight:600; color:#0f172a; word-break:break-word;">{{ $userAgent }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:24px 32px 32px; font-size:13px; line-height:1.7; color:#64748b;">
                                        Email nay duoc gui tu hệ thống <strong>{{ $appName }}</strong>. Vui lòng không tra loi truc tiep vao email tu dong nay.
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


