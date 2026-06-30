<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited to Join CCBRT</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0b6b2c, #065321);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0b6b2c;
        }
        .message {
            margin-bottom: 20px;
            color: #555;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #0b6b2c;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
        }
        .button:hover {
            background-color: #065321;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CCBRT Hospital</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Customer Feedback Management System</p>
        </div>
        <div class="content">
            <p class="greeting">Hello {{ $userName }},</p>
            
            <p class="message">
                You have been invited to join the CCBRT Customer Feedback Management System. This system allows our staff to track, manage, and respond to patient feedback efficiently.
            </p>
            
            <p class="message">
                To complete your registration and set your password, please click the button below:
            </p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Set Your Password</a>
            </div>
            
            <div class="note">
                <strong>Important:</strong> This invitation link will expire in 60 minutes for security reasons.
            </div>
            
            <p class="message">
                If you did not expect this invitation, you can safely ignore this email.
            </p>
            
            <p class="message" style="margin-bottom: 0;">
                Best regards,<br>
                <strong>CCBRT Quality Assurance Team</strong>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} CCBRT Hospital. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
