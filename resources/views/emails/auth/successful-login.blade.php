<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thong bao dang nhap thanh cong</title>
</head>
<body style="margin:0; padding:24px 0; background:#f3f6fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    @php
        $appName = config('app.name', 'HRM');
        $displayName = $user->name ?: $user->email;
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                                <tr>
                                    <td style="padding:28px 32px; background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color:#ffffff;">
                                        <div style="font-size:13px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.9;">{{ $appName }}</div>
                                        <div style="margin-top:10px; font-size:28px; font-weight:700; line-height:1.3;">Dang nhap thanh cong</div>
                                        <div style="margin-top:8px; font-size:15px; opacity:0.92;">He thong da ghi nhan mot phien dang nhap hop le vao tai khoan cua ban.</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:28px 32px 12px;">
                                        <p style="margin:0 0 12px; font-size:16px;">Xin chao <strong>{{ $displayName }}</strong>,</p>
                                        <p style="margin:0; font-size:15px; line-height:1.7; color:#475569;">
                                            Day la email thong bao tu <strong>{{ $appName }}</strong> de xac nhan tai khoan cua ban vua dang nhap thanh cong vao he thong.
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 32px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate; border-spacing:0; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px;">
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b; width:180px;">Thoi gian</td>
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
                                                <td style="padding:18px 20px; font-size:14px; color:#64748b; vertical-align:top;">Trinh duyet / thiet bi</td>
                                                <td style="padding:18px 20px; font-size:15px; font-weight:600; color:#0f172a; word-break:break-word;">{{ $userAgent }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 32px 0;">
                                        <div style="padding:16px 18px; background:#fff7ed; border:1px solid #fdba74; border-radius:14px;">
                                            <div style="font-size:15px; font-weight:700; color:#9a3412; margin-bottom:8px;">Canh bao bao mat</div>
                                            <div style="font-size:14px; line-height:1.7; color:#7c2d12;">
                                                Neu day khong phai la ban, hay doi mat khau ngay, kiem tra cac phien dang nhap bat thuong va lien he quan tri he thong de duoc ho tro.
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:24px 32px 32px; font-size:13px; line-height:1.7; color:#64748b;">
                                        Email nay duoc gui tu he thong <strong>{{ $appName }}</strong>. Vui long khong tra loi truc tiep vao email tu dong nay.
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
