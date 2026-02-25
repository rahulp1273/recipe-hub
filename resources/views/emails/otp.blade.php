<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RecipeHub Verification Code</title>
</head>
<body>
    <h1>RecipeHub Verification Code</h1>

    <p>Use the following one-time password (OTP) to complete your {{ $type === 'register' ? 'registration' : 'login' }}:</p>

    <p style="font-size: 24px; font-weight: bold; letter-spacing: 4px;">
        {{ $otp }}
    </p>

    <p>This code is valid for 5 minutes. If you did not request this, you can safely ignore this email.</p>
</body>
</html>

