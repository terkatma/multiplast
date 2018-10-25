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
/*
    public function sendMail2($ids, $emailTemplate){


        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        if ($emailTemplate == 'invitation')
            Debugger::log('ODESLÁNÍ POZVÁNEK ZÁKAZNÍKŮM====================; ID ');
        else if ($emailTemplate == 'reminder')
            Debugger::log('ODESLÁNÍ UPOMÍNEK ZÁKAZNÍKŮM====================; ID ');

        $sentInvitationCount = 0;
        $date = date("Y");

        foreach ($customers as $customer) {
*/
            /* @var Customer $customer */
/*
            $mail = new Message;
            $template = parent::createTemplate();
            $template->customer = $customer;
            //Debugger::log('ODESÍLÁNÍ [' . $customer->id . '] [' . $customer->email . '] [' . $customer->name . '] [' . $customer->company . ']; ID ');
            if ($customer->language == 'en'){
                $subject = "Christmas party $date";
                $attachement = 'invitation';
            }
            else {
                $subject = "Vánoční večírek $date";
                $attachement = 'pozvánka';
            }
            $mail->setSubject($subject);
            $mail->setFrom('lukas.horn@titan-multiplast.cz', 'Ing. Lukáš Horn');
            $mail->addAttachment("$subject - $attachement.pdf", file_get_contents(__INVITATIONS_DIR__."/" . date("Y") . "/" . $customer->id . ".pdf"));
            $template->setFile(__MAIL_DIR__ . '/Generate/' . $emailTemplate . '_' . $customer->language . '.latte');
            $mail->setHtmlBody($template);

            Debugger::log('OK    Připojena příloha [' . $customer->id . '] ' . $customer->email . '; ID ');

            try {
                $mail->addTo($customer["email"]);
                try {
                    $this->mailer->smtpMailer->send($mail);
                } catch (SendException $e) {
                    Debugger::log($e, 'mailexception');
                }

                if ($emailTemplate == 'invitation'){
                    $this->invitationsRepository->updateCustomerIsSent($customer->id, 1);
                    $this->invitationsRepository->updateCustomerInvitationSentLog($customer->id);
                }
                else if ($emailTemplate == 'reminder'){
                    $this->invitationsRepository->updateCustomerReminderSentLog($customer->id);
                }

                Debugger::log('OK    Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . 'proběhlo v pořádku; ID ');

                $sentInvitationCount++;
            } catch (\Exception $e) {
                $this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
                Debugger::log('ERROR Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');
            }

        }
        $this->presenter->flashMessage("Dokončeno. Odesláno $sentInvitationCount emailů.", 'success');
        $this->presenter->redirect("this");
    }
*/

}
