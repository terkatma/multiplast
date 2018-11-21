<?php


namespace Utils\Email;

use App\Components\BaseComponent;
use Nette\Application\UI;
use Nette\Mail\Message;
use Tracy\Debugger;
use app\entities\Customer;
use Nette\Mail\SendException;
use App\Presenters\HomepagePresenter;

/**
 * Class Email for sending emails
 * @package App\Components
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

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    public $invitationsRepository;

    /**
     * @inject
     * @var \Utils\Email\Email
     */
    public $mailer;

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
