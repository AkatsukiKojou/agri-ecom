<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AgriEcom Email Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f6fef6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 120, 44, 0.08);
            padding: 32px 28px;
        }
        .logo {
            display: block;
            margin: 0 auto 18px auto;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #e8f5e9;
        }
        .title {
            color: #388e3c;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }
        .desc {
            color: #388e3c;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 24px;
        }
        .code-box {
            background: #e8f5e9;
            color: #1b5e20;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 0.3em;
            text-align: center;
            border-radius: 12px;
            padding: 18px 0;
            margin-bottom: 24px;
        }
        .instructions {
            color: #333;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 18px;
        }
        .footer {
            color: #888;
            font-size: 0.95rem;
            text-align: center;
            margin-top: 32px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://i.imgur.com/2QZb6hL.png" alt="AgriEcom Logo" class="logo">
        <div class="title">AgriEcom</div>
        <div class="desc">Your Gateway to Modern Agriculture</div>
        <div class="instructions">Please use the verification code below to verify your email address:</div>
        <div class="code-box">{{ $code }}</div>
        <div class="instructions">Copy and paste this code into the verification page to complete your registration.</div>
        <div class="footer">Thank you for joining AgriEcom!<br>For support, reply to this email.</div>
    </div>
</body>
</html>