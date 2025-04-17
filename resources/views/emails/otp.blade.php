<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333;">🔐 Password Reset Request</h2>
        <p style="font-size: 16px; color: #555;">
            Your password reset code is:
        </p>
        <div style="font-size: 24px; font-weight: bold; background-color: #f0f0f0; padding: 12px; text-align: center; border-radius: 6px; margin: 10px 0;">
            {{ $otp }}
        </div>
        <p style="font-size: 16px; color: #555;">
            This code is valid for <strong>30 minutes</strong>.
        </p>
        <p style="font-size: 14px; color: #999;">
            If you didn't request a password reset, please ignore this message.
        </p>
        <hr style="margin-top: 30px; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #bbb; text-align: center;">
            &copy; {{ date('Y') }} Your App Name. All rights reserved.
        </p>
    </div>
</body>
</html>
