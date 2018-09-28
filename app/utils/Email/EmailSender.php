<?php


namespace Utils\Email;

use Nette\Mail\Message;
use Tracy\Debugger;

/**
 * Class Email for sending emails
 * @package Utils\Email
 */
class Email
{

    /**
     * @var \Nette\Mail\IMailer
     * classic SMTP Mailer
     */
    public $smtpMailer;
    public $sender;

    const SENDMAIL = "SENDMAIL";

    /**
     * Creates SMTP Mailer
     */
    public function __construct(\Nette\Mail\IMailer $mailer)
    {
        $this->smtpMailer = $mailer;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }
}
