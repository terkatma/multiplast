<?php

namespace App\Components;

use App\Presenters\HomepagePresenter;
use Nette\Forms\Form;
use Nette\Mail\SendException;
use Nette\Utils\Validators;
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
    private $sex;
    private $language;

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
        $is_sent = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $is_answered = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $this->sex = [0 => 'Muž', 1 => 'Žena'];
        $this->language = ['cz' => 'cz', 'en' => 'en'];

        /**
         * @var HomepagePresenter $p
         */
        $p = $this->getPresenter();
        /**
         * Columns
         */
        $grid->addColumnNumber("id", "Id")
            ->setDefaultHide();

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
                if(Validators::isEmail($value))
                    $this->invitationsRepository->updateCustomerEmail($id, $value);
                else
                    $this->presenter->flashMessage("Zadán neplatný email.", 'error');
                    $this->presenter->redirect("this");
            });

        $grid->addColumnNumber('invitation_count', 'Pozvaných')
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerInvitationCount($id, $value);
            });

        $grid->addColumnNumber('ticket_count', 'Potvrzených')
            ->setFilterSelect($ticket_count);

        $grid->addColumnText("note", "Poznámka zákazníka");

        $grid->addColumnNumber("is_sent", "Odesláno")
            ->setReplacement($is_sent)
            ->setFilterSelect($is_sent);

        $grid->addColumnNumber("is_answered", "Odpověď")
            ->setReplacement($is_answered)
            ->setFilterSelect($is_answered);

        $grid->addColumnDateTime("reply_deadline", "Termín odpovědi")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerReplyDeadline($id, $value);
            })
            ->setFormat('d.m.Y');

        $grid->addColumnText("is_woman","Pohlaví")
            ->setEditableInputTypeSelect($this->sex)
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerIsWoman($id, $value);
            })
            ->setReplacement($this->sex);

        $grid->addColumnText("language", "Jazyk")
            ->setEditableInputTypeSelect($this->language)
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerLanguage($id, $value);
            });

        $grid->addColumnText("hash", "url")
            ->setDefaultHide();
/*
        $grid->addColumnText("user_note", "Poznámka")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->updateCustomerUserNote($id, $value);
            });
*/
        /*
         * Group Action
         */
        $grid->addGroupAction('odeslat')->onSelect[] = [$this, 'sendMail'];
        //$grid->addGroupAction('vygenerovat hash')->onSelect[] = [$this, 'generateHash'];
        $grid->addGroupAction('vygenerovat PDF')->onSelect[] = [$this, 'generatePDFs'];

        /*
         * Inline Add
         */
        $grid->addInlineAdd()
            ->onControlAdd[] = function(Container $container){
            $container->addText('name');
            $container->addText('addressing');
            $container->addText('company');

            $container->addText('email')
                ->setRequired('Zadejte email')
                //->setEmptyValue('@')
                ->addRule(Form::MAX_LENGTH, 'Maximální délka emailu je %d znaků', 30)
                ->addRule(Form::EMAIL, 'Zadán neplatný email.');

            $container->addInteger('invitation_count');

            $container->addText('reply_deadline', '')
                ->setType('date');

            $container->addSelect('is_woman', '', $this->sex);
            $container->addSelect('language', '', $this->language);
            $container->addText('user_note');
        };

        $grid->getInlineAdd()->setPositionTop('FALSE');

        $grid->getInlineAdd()->onSubmit[] = function ($values) use ($p) {
            if ($values["name"] && $values["addressing"] && $values["company"] && $values["email"]
                && $values["invitation_count"] && $values["reply_deadline"]  && $values["language"]){
                $p->handleCreateCustomer($values);
                $p->flashMessage("Zákazník " . $values["name"] . " " . $values["company"] . " " .  $values["email"] . " přidán.", 'success');
                $p->redirect("this");
            } else {
                $p->flashMessage("Chyba. Vyplňte všechna pole.", 'error');
                $p->redirect("this");
            }
        };


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
        $date = date("Y");

        foreach ($customers as $customer) {
            /* @var Customer $customer */
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
            $template->setFile(__MAIL_DIR__ . '/Generate/invitation_' . $customer->language . '.latte');
            $mail->setHtmlBody($template);

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
