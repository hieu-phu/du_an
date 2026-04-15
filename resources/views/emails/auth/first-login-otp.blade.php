<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { border: 1px solid #e5e7eb; border-top: none; padding: 30px; border-radius: 0 0 8px 8px; }
        .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #1d4ed8; letter-spacing: 5px; margin: 20px 0; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xac thuc dang nhap dau tien</h1>
        </div>
        <div class="content">
            <p>Xin chao,</p>
            <p>He thong yeu cau xac minh OTP cho lan dang nhap dau tien cua tai khoan. Vui long nhap ma ben duoi de tiep tuc:</p>

            <div class="otp-code">{{ $otp }}</div>

            <p>Ma nay se het han sau 15 phut. Neu ban khong thuc hien dang nhap, vui long bo qua email nay.</p>
            <p>Tran trong,<br>Doi ngu {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            Day la email tu dong, vui long khong tra loi.
        </div>
    </div>
</body>
</html>
