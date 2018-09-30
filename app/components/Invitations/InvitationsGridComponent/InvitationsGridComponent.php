<?php

namespace App\Components;

use App\Presenters\HomepagePresenter;
use Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Utils\Random;
use app\entities\Customer;
use Tracy\Debugger;


/**
 * Class InvitationAnswerComponent
 * @package App\Components
 */
class InvitationsGridComponent extends BaseGridComponent
{

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    public $invitationsRepository;

    public $all_tickets = [];

    /**
     * @inject
     * @var \Utils\Email\Email
     */
    public $mailer;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     * @return DataGrid
     */
    public function createComponentInvitationsGrid($name)
    {

        /**
         * @var DataGrid $grid
         */
        $grid = $this->getGrid($name);
        $grid->setDataSource($this->invitationsRepository->findAll());

        $ticket_count = ['' => 'Vše', 0 => 'Odmítli', 1 => '1', 2 => '2'];
        $is_sent = $is_sent = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $is_answered = $is_answered = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];

        /**
         * Columns
         */
        $grid->addColumnText("name", "Jméno")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerName($id, $value);
            });
        $grid->addColumnText("addressing", "Oslovení")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerAddressing($id, $value);
            });
        $grid->addColumnText("company", "Firma")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerCompany($id, $value);
            });
        $grid->addColumnText("email", "E-mail")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerEmail($id, $value);
            });
        //$grid->addColumnText( "email2", "E-mail", "email");
        //$grid->addColumnNumber("ticket_count", "Počet lístků");
        //$grid->addColumnText('invitation_count', 'Počet pozvaných');
        $grid->addColumnText('ticket_count', 'Potvrzených')->setFilterSelect($ticket_count);
        $grid->addColumnText("note", "Poznámka");
        $grid->addColumnText("is_sent", "Odesláno")->setReplacement($is_sent)->setFilterSelect($is_sent);
        $grid->addColumnText("is_answered", "Odpověď")->setReplacement($is_answered)->setFilterSelect($is_answered);
        $grid->addColumnDateTime("reply_deadline", "Termín odpovědi")
            ->setFormat('Y-m-d')
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerReplyDeadline($id, $value);
            });
        $grid->addColumnText("is_woman", "0-muž, 1-žena")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerIsWoman($id, $value);
            });
        $grid->addColumnText("language", "Jazyk")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerLanguage($id, $value);
            });
        $grid->addGroupAction('odeslat')->onSelect[] = [$this, 'sendMail'];
        //$grid->addGroupAction('vygenerovat hash')->onSelect[] = [$this, 'generateHash'];
        $grid->addGroupAction('vygenerovat PDF')->onSelect[] = [$this, 'generatePDFs'];

        return $grid;
    }

    public function sendMail($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        Debugger::log('===== ODESLÁNÍ POZVÁNEK ZÁKAZNÍKŮM ========================================; ID ');
        foreach ($customers as $customer) {
            /* @var Customer $customer */
            $mail = new Message;
            $mail->setSubject("Vánoční večírek 2018");
            $mail->setFrom('monika.drobna86@gmail.com', 'Lukáš');
            $template = parent::createTemplate();
            $template->customer = $customer;

            $template->setFile(__MAIL_DIR__ . '/Generate/invitation.latte');

            $mail->setHtmlBody($template);

            $mail->addAttachment("Vánoční večírek " . date("Y") . " - pozvánka.pdf", file_get_contents(__INVITATIONS_DIR__."/" . date("Y") . "/" . $customer->id . ".pdf"));

            try {
                $mail->addTo($customer["email"]);
                $this->mailer->smtpMailer->send($mail);
                $this->invitationsRepository->updateCustomerIsSent($customer->id, 1);
                //$this->presenter->flashMessage("Mail zákazníkovi [$customer->name] [$customer->company] úspěšně odeslán", "success");
                Debugger::log('OK    Odeslání mailu zákazníkovi ' . $customer->id . ' ' . $customer->email . '; ID ');
            } catch (\Exception $e) {
                $this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
                Debugger::log('ERROR Odeslání mailu zákazníkovi ' . $customer->id . ' ' . $customer->email . ' se nezdařilo; ID ');
            }

        }
        $this->presenter->flashMessage("Dokončeno", 'success');
        $this->presenter->redirect("this");
    }

    public function generateHash($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();

        foreach ($customers as $customer) {
            $hash = Random::generate(6);
            while ($this->invitationsRepository->checkKeyDuplicity($hash)) {
                $hash = Random::generate(6);
            }
        $this->invitationsRepository->updateCustomersHash($customer->id, $hash);
        }
        $this->getPresenter()->flashMessage('Uloženo', 'success' );
        $this->presenter->redirect("this");
    }

    public function generatePDFs($ids)
    {
        /* @var HomepagePresenter $presenter*/
        $presenter = $this->presenter;
        $presenter->handleGeneratePdf($ids);
        $this->presenter->flashMessage("PDF úspěšně vygenerovány.", "success");
        $this->presenter->redirect("this");
    }
}
