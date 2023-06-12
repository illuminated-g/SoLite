<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
// Alias the League Google OAuth2 provider class
use League\OAuth2\Client\Provider\Google;

class Mailer {
    public function createMailer() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        //$mail->AuthType = 'XOAUTH2';
        $mail->Username = $_ENV['MAIL_EMAIL'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];

        $provider = new Google(
            [
                'clientId' => $_ENV['MAIL_CLIENTID'],
                'clientSecret' => $_ENV['MAIL_CLIENTSECRET'],
            ]
        );
        
        /*$mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $_ENV['MAIL_CLIENTID'],
                    'clientSecret' => $_ENV['MAIL_CLIENTSECRET'],
                    'refreshToken' => $_ENV['MAIL_REFRESHTOKEN'],
                    'userName' => $_ENV['MAIL_EMAIL'],
                ]
            )
        );*/

        return $mail;
    }
}