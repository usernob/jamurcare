<?php

// tambahan sutan
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #86af7b, #6b8e73);
            padding: 20px;
            text-align: center;
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 30px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #86af7b, #6b8e73);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .button:hover {
            background: linear-gradient(135deg, #7a9d6f, #5d7b64);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.2);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .logo {
            height: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://via.placeholder.com/120x40?text=Jamur+Care" alt="Jamur Care Logo" class="logo">
        <h1>Password Reset</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <p>Click the button below to reset your password:</p>
        
        <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        
        <p>This password reset link will expire in 60 minutes.</p>
        
        <p>If you did not request a password reset, no further action is required.</p>
        
        <p>Thank you,<br>The Jamur Care Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Jamur Care. All rights reserved.</p>
        <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
        <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    </div>
</body>
</html>