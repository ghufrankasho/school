<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background-color: #4CAF50;
        color: white;
        text-align: center;
        padding: 20px 0;
    }

    .content {
        padding: 20px;
    }

    .content p {
        line-height: 1.6;
        color: #333333;
    }

    .content strong {
        color: #4CAF50;
    }

    .footer {
        text-align: center;
        padding: 10px;
        background-color: #f1f1f1;
        border-top: 1px solid #dddddd;
    }

    .footer p {
        margin: 0;
        color: #777777;
        font-size: 12px;
    }

    .button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        color: white;
        background-color: #4CAF50;
        border-radius: 4px;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p>Your password reset code is: <strong>{{ $code }}</strong></p>
            <p>If you did not request a password reset, no further action is required.</p>
            <p>Thank you,<br>{{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>