<?php

namespace Natsu\Model;

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class EmailModel extends EntityModel {

    const
            REGISTER_CMPL = "REGISTER_CMPL",
            FORGET_PASSWD = "FORGET_PASSWD",
            TICKET_CMPL = "TICKET_CMPL",
            FROM = "butaneko@natsucon.cz",
            PAYMENT_CMFR = "PAYMENT_CMFR";

    public function getTemplate($code) {
        $row = $this->database->select("*")->from($this->table)->where("code = ?", $code)->fetch();
        return $row;
    }

    public function replace($replace, $subject) {
        foreach ($replace as $key => $value) {
            $subject = str_replace($key, $value, $subject);
        }
        return $subject;
    }

    public function sendEmail($from, $to, $template) {
        $mail = new Message;
        $mail->setFrom($from)
                ->addTo($to)
                ->setSubject($template->subject)
                ->setHtmlBody($template->body);
         
       // dump($from); dump($to); dump($template); exit;
        $mailer = new SendmailMailer;
        $mailer->send($mail);
    }

}
