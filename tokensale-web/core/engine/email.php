<?php

namespace core\engine;

class Email
{

    static private $_instance;

    static private function inst()
    {
        if (is_null(self::$_instance)) {
            require(PATH_TO_THIRD_PARTY_DIR . '/PHPMailer/PHPMailerAutoload.php');
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
    }

    static public function send($to, $copyTo, $subject, $message, $files = array())
    {
        self::inst();

        $from = EMAIL_FROM_EMAIL;
        $smtp_host = EMAIL_HOST;
        $smtp_login = EMAIL_LOGIN;
        $smtp_password = EMAIL_PASSWORD;
        $smtp_port = EMAIL_PORT;
        $secure = 'ssl';

        $reply_to = EMAIL_REPLY_TO_EMAIL;

        $mail = new \PHPMailer;

        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false
            )
        );

        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = $smtp_host;
        $mail->Port = $smtp_port;
        $mail->SMTPSecure = $secure;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_login;
        $mail->Password = $smtp_password;
        $mail->setFrom($from, EMAIL_FROM_NAME);
        $mail->addReplyTo($reply_to, EMAIL_REPLY_TO_NAME);

        if (strlen($to) < 2 && count($copyTo) > 0) { 
            $to = array_shift($copyTo);
        }

        $mail->addAddress($to);

        foreach ($copyTo as $addr) {
            $mail->addCC($addr);
        }

        $mail->Subject = $subject;

        $mail->msgHTML($message);

        foreach ($files as $file) {
            $mail->addAttachment($file);
        }

        return $mail->send();
    }
}