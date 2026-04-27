<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-top: 4px solid white;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #8b5cf6;
        }
        .credentials-box {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 30px 0;
            border-left: 4px solid #8b5cf6;
        }
        .credential-row {
            margin-bottom: 15px;
        }
        .credential-row:last-child {
            margin-bottom: 0;
        }
        .credential-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: block;
        }
        .credential-value {
            background-color: white;
            padding: 10px 12px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
        }
        .cta-button {
            display: inline-block;
            background-color: #8b5cf6;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            margin: 30px 0;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .cta-button:hover {
            background-color: #7c3aed;
        }
        .button-container {
            text-align: center;
        }
        .important-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .important-note strong {
            color: #d97706;
        }
        .important-note ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        .important-note li {
            margin-bottom: 8px;
            color: #333;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 20px 0;
        }
        p {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Main Header --}}
        <div class="header">
            <h1>Welcome to OJT System</h1>
            <p style="opacity: 0.9; margin: 0;">Your coordinator account is ready</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <p class="greeting">Hello <strong>{{ $coordinatorName }}</strong>,</p>

            <p>Your coordinator account has been successfully created in the OJT System. Your login credentials are provided below for your first access:</p>

            {{-- Credentials Box --}}
            <div class="credentials-box">
                <div class="credential-row">
                    <span class="credential-label">Username/Email</span>
                    <div class="credential-value">{{ $username }}</div>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Password</span>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>

            {{-- Login Button --}}
            <div class="button-container">
                <a href="{{ $loginUrl }}" class="cta-button">Login to OJT System</a>
            </div>

            {{-- Important Note --}}
            <div class="important-note">
                <strong>⚠️ Important Security Notice:</strong>
                <ul>
                    <li><strong>Change your password</strong> immediately after your first login</li>
                    <li><strong>Keep these credentials secure</strong> and never share them with anyone</li>
                    <li><strong>Do not reply</strong> to this email with sensitive information</li>
                    <li>If you did not request this account, please contact the system administrator immediately</li>
                </ul>
            </div>

            <p>If you experience any issues logging in or need assistance, please contact the system administrator.</p>

            <div class="divider"></div>

            {{-- Footer --}}
            <div class="footer">
                <p>This is an automated message from the OJT System. Please do not reply directly to this email.</p>
                <p style="margin-top: 10px;">&copy; 2025 PHILCST CCS OJT System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
