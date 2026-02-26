<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .otp-code {
            background: #667eea;
            color: white;
            font-size: 32px;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            letter-spacing: 3px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🔐 Kode Verifikasi OTP</h1>
        <p>Workshop Web Framework</p>
    </div>

    <div class="content">
        <p>Halo, {{ $user->name }}!</p>

        <p>Anda melakukan login menggunakan Google. Untuk melanjutkan, silakan gunakan kode verifikasi berikut:</p>

        <div class="otp-code">
            {{ $otpCode }}
        </div>

        <p><strong>⏰ Kode ini akan berlaku selama 5 menit.</strong></p>

        <p>Jika Anda tidak melakukan login, abaikan email ini.</p>

        <p>Terima kasih,<br>Tim Workshop Web Framework</p>
    </div>

    <div class="footer">
        <p>&copy; 2026 Workshop Web Framework. All rights reserved.</p>
    </div>
</body>

</html>