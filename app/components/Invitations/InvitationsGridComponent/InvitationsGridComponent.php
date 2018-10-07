<?php

namespace App\Components;

use App\Presenters\HomepagePresenter;
use Nette\Mail\SendException;
use Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Utils\Random;
use app\entities\Customer;
use Tracy\Debugger;
use Nette\Forms\Container;

/**
 * Class InvitationAnswerComponent
 * @property array|mixed onClick
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
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentInvitationsGrid($name)
    {

        /**
         * @var DataGrid $grid
         */
        $grid = $this->getGrid($name);
        $grid->setDataSource($this->invitationsRepository->findAll());
        $grid->setColumnsHideable();

        $ticket_count = ['' => 'Vše', 0 => 'Odmítli', 1 => '1', 2 => '2'];
        $is_sent = $is_sent = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $is_answered = $is_answered = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];

        $p = $this->getPresenter();
        /**
         * Columns
         */
        $grid->addColumnNumber("id", "Id");

        $grid->addColumnText("name", "Jméno")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerName($id, $value);
            });

        $grid->addColumnText("addressing", "Oslovení")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerAddressing($id, $value);
            })
            ->setSortable();

        $grid->addColumnText("company", "Firma")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerCompany($id, $value);
            })
            ->setSortable();

        $grid->addColumnText("email", "E-mail")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerEmail($id, $value);
            });

        //$grid->addColumnText( "email2", "E-mail", "email");
        //$grid->addColumnNumber("ticket_count", "Počet lístků");

        $grid->addColumnNumber('invitation_count', 'Pozvaných')
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerInvitationCount($id, $value);
            });

        $grid->addColumnNumber('ticket_count', 'Potvrzených')
            ->setFilterSelect($ticket_count);

        $grid->addColumnText("note", "Poznámka");

        $grid->addColumnNumber("is_sent", "Odesláno")
            ->setReplacement($is_sent)
            ->setFilterSelect($is_sent);

        $grid->addColumnNumber("is_answered", "Odpověď")
            ->setReplacement($is_answered)
            ->setFilterSelect($is_answered);

        $grid->addColumnText("reply_deadline", "Termín odpovědi")
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

        $grid->addColumnText("hash", "Adresa");

        $grid->addGroupAction('odeslat')->onSelect[] = [$this, 'sendMail'];

        //$grid->addGroupAction('vygenerovat hash')->onSelect[] = [$this, 'generateHash'];

        $grid->addGroupAction('vygenerovat PDF')->onSelect[] = [$this, 'generatePDFs'];

        /*
        $grid->addInlineAdd()
            ->onControlAdd[] = function(Container $container){
            $container->addText('id')->setAttribute('readonly');
            $container->addText('name');
            $container->addText('addressing');
            $container->addText('company');
            $container->addText('email');
            $container->addText('invitation_count');
            $container->addText('reply_deadline');
            $container->addText('is_woman');
            $container->addText('language');
            $container->addText('hash');
        };

        $grid->getInlineAdd()->setPositionTop('FALSE');

        $grid->getInlineAdd()->onSubmit[] = function ($values) use ($p) {
            if ($values["id"] && $values["name"] && $values["adressing"] && $values["company"] && $values["email"]
                && $values["invitation_count"] && $values["reply_deadline"] && $values["is_woman"] && $values["language"]
                && $values["hash"]){
                $p->handleCreateCustomer($values);
                $p->flashMessage("Hra vytvořena.", 'success');
                $p->redrawControl("flashMessages");
            } else {
                $p->flashMessage("Vyplň údaje.", 'error');
                $p->redrawControl("flashMessages");
            }
        };
        */

        $grid->addActionCallback('delete', '')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setClass('btn btn-xs btn-danger ajax')
            ->setConfirm('Opravdu chcete smazat zákazníka %s?', 'name')
            ->onClick[] = function ($id) {
            $this->invitationsRepository->deleteCustomer($id);
            $this->presenter->flashMessage("Zákazník [$id] smazán.",'success');
            $this->presenter->redirect("this");
        };

        $grid->setColumnsSummary(['invitation_count','ticket_count', 'is_sent','is_answered']);

        return $grid;
    }

    public function sendMail($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        Debugger::log('ODESLÁNÍ POZVÁNEK ZÁKAZNÍKŮM====================; ID ');
        $sentInvitationCount = 0;

        foreach ($customers as $customer) {
            /* @var Customer $customer */
            $mail = new Message;
            //Debugger::log('ODESÍLÁNÍ [' . $customer->id . '] [' . $customer->email . '] [' . $customer->name . '] [' . $customer->company . ']; ID ');
            if ($customer->language == 'en'){
                $mail->setSubject("Christmas party 2018");
                $mail->setFrom('lukas.horn@titan-multiplast.cz', 'Ing. Lukáš Horn');
                $template = parent::createTemplate();
                $template->customer = $customer;

                $template->setFile(__MAIL_DIR__ . '/Generate/invitation_en.latte');
                $mail->setHtmlBody($template);
                $mail->addAttachment("Christmas party " . date("Y") . " - invitation.pdf", file_get_contents(__INVITATIONS_DIR__."/" . date("Y") . "/" . $customer->id . ".pdf"));
            }
            else {
                $mail->setSubject("Vánoční večírek 2018");
                $mail->setFrom('lukas.horn@titan-multiplast.cz', 'Ing. Lukáš Horn');
                $template = parent::createTemplate();
                $template->customer = $customer;

                $template->setFile(__MAIL_DIR__ . '/Generate/invitation.latte');
                $mail->setHtmlBody($template);
                $mail->addAttachment("Vánoční večírek " . date("Y") . " - pozvánka.pdf", file_get_contents(__INVITATIONS_DIR__."/" . date("Y") . "/" . $customer->id . ".pdf"));
            }
            Debugger::log('OK    Připojena příloha [' . $customer->id . '] ' . $customer->email . '; ID ');

            try {
                $mail->addTo($customer["email"]);
                try {
                    $this->mailer->smtpMailer->send($mail);
                } catch (SendException $e) {
                    Debugger::log($e, 'mailexception');
                }

                $this->invitationsRepository->updateCustomerIsSent($customer->id, 1);
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
