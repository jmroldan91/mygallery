<?php

class Mail {
    private $cliente, $from, $to, $alias, $subject, $message, $attachment = [];
    
    function __construct($to=null,  $subject=null, $message=null, $from=null, $alias=null, $attachment=[]) {
        $this->cliente = new Google_Client();
        $this->cliente->setApplicationName('CorreoPHP');
        $this->cliente->setClientId('536060712172-mguuql9ce6jv77cu8c4kmheo4irc59sl.apps.googleusercontent.com');
        $this->cliente->setClientSecret('m-VafCFc8svPQaG_X8Dh_zDk');
        $this->cliente->setRedirectUri('https://usergest-jmroldan00.c9users.io/mail/oauth/guardar.php');
        $this->cliente->setScopes('https://www.googleapis.com/auth/gmail.compose');
        $this->cliente->setAccessToken(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/mail/oauth/token.conf'));
        $this->from = $from;
        $this->to = $to;
        $this->alias = $alias;
        $this->subject = $subject;
        $this->message = $message;
        $this->attachment = $attachment;
    }
    
    function getFrom() {
        return $this->from;
    }

    function getTo() {
        return $this->to;
    }

    function getSubject() {
        return $this->subject;
    }

    function getMessage() {
        return $this->message;
    }

    function getAttachment() {
        return $this->attachment;
    }

    function setFrom($from) {
        $this->from = $from;
    }

    function setTo($to) {
        $this->to = $to;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setAttachment($attachment) {
        $this->attachment = $attachment;
    }

    function send(){
        if ($this->cliente->getAccessToken()) {
            $service = new Google_Service_Gmail($this->cliente);
            try {
                $mail = new PHPMailer();
                $mail->CharSet = "UTF-8";
                $mail->From = $this->from ? null : Constant::_MAILFROM;
                $mail->FromName = $this->alias ? null : Constant::_MAILALIAS;
                $mail->AddAddress($this->to);
                $mail->AddReplyTo($this->from ? null : Constant::_MAILFROM, $this->alias ? null : Constant::_MAILALIAS);
                $mail->Subject = $this->subject;
                $mail->Body = $this->message;
                $mail->preSend();
                $mail->isHTML();
                $mime = $mail->getSentMIMEMessage();
                $mime = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');
                $mensaje = new Google_Service_Gmail_Message();
                $mensaje->setRaw($mime);
                $service->users_messages->send('me', $mensaje);
                $r = 1;
            } catch (Exception $e) {
                print($e->getMessage());
                $r = 0;
            }
        }else{
             $r = -1;
        }
        return $r;
    }

}
