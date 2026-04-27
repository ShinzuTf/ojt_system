<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CoordinatorMailService
{
    /**
     * Send welcome email using PHPMailer with detailed error logging
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

            Log::info('Starting email send', [
                'recipient' => $coordinatorEmail,
                'mailer_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'scheme' => config('mail.mailers.smtp.scheme'),
                ]
            ]);

            // Server settings
            $mail->isSMTP();
            $mail->SMTPDebug = 2;  // Enable verbose debugging
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            // Use TLS encryption for Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = config('mail.mailers.smtp.port');

            // Set timeout
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = true;

            // Sender and recipient
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($coordinatorEmail, $coordinatorName);

            // Email subject
            $mail->Subject = 'Your OJT System Coordinator Account Has Been Created';

            // Get email body from template
            $emailBody = View::make('emails.coordinator-welcome', [
                'coordinatorName' => $coordinatorName,
                'username' => $username,
                'password' => $password,
                'loginUrl' => config('app.url') . '/login',
                
            ])->render();

            $mail->isHTML(true);
            $mail->Body = $emailBody;
            $mail->AltBody = self::getPlainTextTemplate($coordinatorName, $username, $password);

            // Send the email
            $result = $mail->send();

            Log::info('Coordinator welcome email sent successfully', [
                'email' => $coordinatorEmail,
                'name' => $coordinatorName,
                'timestamp' => now(),
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Coordinator email sending failed - PHPMailer Exception', [
                'email' => $coordinatorEmail,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Coordinator email sending failed - General Exception', [
                'email' => $coordinatorEmail,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Get plain text email template
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

Best regards,
PHILCST CCS OJT System
TEXT;
    }
}


