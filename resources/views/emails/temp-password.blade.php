<!DOCTYPE html>
<html>
<head>
    <title>Temporary Password</title>
</head>
<body>
    <p>Hi {{ $name }},</p>

    <p>You have requested to reset your password. Here is your temporary password:</p>

    <p><strong>{{ $temporaryPassword }}</strong></p>

    <p>Please log in with this password and change it immediately after logging in.</p>

    <p>Thank you,<br>The Supervisor Hunting System Team</p>
</body>
</html>