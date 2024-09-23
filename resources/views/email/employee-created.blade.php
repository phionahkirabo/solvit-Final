<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to the Company</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        a {
            color: #1a73e8;
            text-decoration: none;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Company, {{ $employee->employee_name }}!</h1>
        <p>We're excited to have you join our team.</p>
        <p>Your default password is: <strong>{{ $defaultPassword }}</strong></p>
        <p>To set a new password, please click the link below:</p>
        <p><a href="{{ $verificationLink }}" target="_blank">Set New Password</a></p>
        <p>If you have any questions or need assistance, feel free to reply to this email.</p>
        <div class="footer">
            <p>Best regards,</p>
            <p>The Company Team</p>
            <p><a href="mailto:support@yourcompany.com">support@yourcompany.com</a></p>
        </div>
    </div>
</body>
</html>
