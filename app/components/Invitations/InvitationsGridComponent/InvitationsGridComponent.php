<?php

namespace App\Components;

use App\Presenters\HomepagePresenter;
use Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Utils\Random;
use app\entities\Customer;


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
        $grid->addColumnText("name", "Jméno");
        $grid->addColumnText("addressing", "Oslovení");
        $grid->addColumnText("company", "Firma");
        $grid->addColumnText("email", "E-mail");
        //$grid->addColumnText( "email2", "E-mail", "email");
        //$grid->addColumnNumber("ticket_count", "Počet lístků");
        $grid->addColumnText('invitation_count', 'Počet pozvaných');
        $grid->addColumnText('ticket_count', 'Počet potvrzených lístků')->setFilterSelect($ticket_count);
        $grid->addColumnText("note", "Poznámka");
        $grid->addColumnText("is_sent", "Odesláno")->setReplacement($is_sent)->setFilterSelect($is_sent);
        $grid->addColumnText("is_answered", "Odpověď")->setReplacement($is_answered)->setFilterSelect($is_answered);
        $grid->addGroupAction('odeslat')->onSelect[] = [$this, 'sendMail'];
        //$grid->addGroupAction('vygenerovat hash')->onSelect[] = [$this, 'generateHash'];
        $grid->addGroupAction('vygenerovat PDF')->onSelect[] = [$this, 'generatePDFs'];

        return $grid;
    }

    public function sendMail($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();

        foreach ($customers as $customer) {
            /* @var Customer $customer */
            $mail = new Message;
            $mail->setSubject("Vánoční večírek 2018");
            $mail->setFrom('monika.drobna86@gmail.com', 'Lukáš');
            $template = parent::createTemplate();
            $template->customer = $customer;

            $template->setFile(__MAIL_DIR__ . '/Generate/invitation.latte');

            $mail->setHtmlBody($template);
            //$mail->addAttachment("Projekt Cerberus.pdf", file_get_contents(__ROOT_DIR__ . __ATACHDIR__ . "../cerberus/" . $member->year . "/" . $member->turnus . "/" . $member->participant_id . ".pdf"));

            $mail->addTo($customer["email"]);

            $this->mailer->smtpMailer->send($mail);
            //Debugger::log('Odeslání Cerberus mailu účastníkovi ' . $customer->name . ' ' . $customer->company . '; ID ', "cerberusMails");

        }
        $this->presenter->flashMessage("Maily úspěšně odeslány", "success");
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
