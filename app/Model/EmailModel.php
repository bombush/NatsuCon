<?php

namespace Natsu\Model;
use Nette;

class EmailModel extends EntityModel {

   const
       REGISTER_CMPL = "REGISTER_CMPL",
       FORGET_PASSWD = "FORGET_PASSWD",
       TICKET_CMPL = "TICKET_CMPL",
       PAYMENT_CMFR = "PAYMENT_CMFR";

    public function getTemplate($code){
        $row = $this->database->select("*")->from($this->table)->where("code = ?", $code)->fetch();
        return $row;
    }

    public function sendEmail($from, $to, $template){

    }
}

