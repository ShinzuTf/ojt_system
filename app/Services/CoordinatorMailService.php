<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class CoordinatorMailService
{
    /**
     * Send welcome email to newly created coordinator account using PHPMailer
     * 
     * @param string $coordinatorEmail
     * @param string $username
     * @param string $password
     * @param string $coordinatorName
     * @return bool
     */
    public static function sendWelcomeEmail($coordinatorEmail, $username, $password, $coordinatorName)
    {
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = config('mail.host', env('MAIL_HOST'));
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.username', env('MAIL_USERNAME'));
            $mail->Password = config('mail.password', env('MAIL_PASSWORD'));
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = config('mail.port', env('MAIL_PORT', 587));

            // Recipients
            $mail->setFrom(config('mail.from.address', env('MAIL_FROM_ADDRESS')), config('mail.from.name', env('MAIL_FROM_NAME', 'OJT System')));
            $mail->addAddress($coordinatorEmail, $coordinatorName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OJT System Coordinator Account Has Been Created';
            
            // Encode images to base64
            $schoolLogoPath = public_path('images/schoollogo.png');
            $philcstLogoPath = public_path('images/philcst_logo.png');
            
            $schoolLogoBase64 = '';
            $philcstLogoBase64 = '';
            
            if (file_exists($schoolLogoPath)) {
                $schoolLogoBase64 = base64_encode(file_get_contents($schoolLogoPath));
            }
            if (file_exists($philcstLogoPath)) {
                $philcstLogoBase64 = base64_encode(file_get_contents($philcstLogoPath));
            }
            
            // Get the rendered email template with base64 encoded images
            $emailBody = view('emails.coordinator-welcome', [
                'coordinatorName' => $coordinatorName,
                'username' => $username,
                'password' => $password,
                'loginUrl' => config('app.url') . '/login',
                'schoolLogoBase64' => $schoolLogoBase64,
                'philcstLogoBase64' => $philcstLogoBase64,
            ])->render();
            
            $mail->Body = $emailBody;
            $mail->AltBody = self::getPlainTextTemplate($coordinatorName, $username, $password);

            return $mail->send();
            
        } catch (Exception $e) {
            Log::error('Coordinator email sending failed: PHPMailer Error - ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Coordinator email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get plain text email template for alt body
     */
    private static function getPlainTextTemplate($coordinatorName, $username, $password)
    {
        $loginUrl = config('app.url') . '/login';
        
        return <<<TEXT
Welcome to OJT System

Hello {$coordinatorName},

Your coordinator account has been successfully created in the OJT System. Below are your login credentials:

Username/Email: {$username}
Password: {$password}

You can log in to the system at: {$loginUrl}

Important Security Notice:
- Please change your password immediately after your first login
- Keep these credentials secure and never share them with anyone
- Do not reply to this email with sensitive information
- If you did not request this account, contact the system administrator immediately

If you experience any issues logging in or need assistance, please contact the system administrator.

This is an automated message. Please do not reply directly to this email.
© 2025 PHILCST CCS OJT System. All rights reserved.
TEXT;
    }
}

