<?php

namespace App\Components;

use App\Presenters\HomepagePresenter;
use Nette\Database\Table\Selection;
use Nette\Mail\SendException;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;
use Ublaboo\DataGrid\DataGrid;
use Nette\Mail\Message;
use Nette\Utils\Random;
use app\entities\Customer;
use Tracy\Debugger;
use Nette\Forms\Container;
use Endroid\QrCode\QrCode;

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
        //$grid->setPagination(FALSE);

        $ticket_count = ['' => 'Vše', 0 => 'Odmítli', 1 => '1', 2 => '2'];
        $is_sent = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $participated = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $is_answered = ['' => 'Vše', 0 => 'Ne', 1 => 'Ano'];
        $this->sex = [0 => 'Muž', 1 => 'Žena'];
        $this->language = ['cz' => 'cz', 'en' => 'en'];
        $invitation_sent_log = ['' => 'Neodesláno', NULL => 'Vše'];
        $answer_log = [1 => 'Vše', NULL => 'Neodesláno'];
        $reminder_sent_log = [1 => 'Vše', NULL => 'Neodesláno'];
        $confirmation_sent_log = [1 => 'Vše', NULL => 'Neodesláno'];

        /**
         * @var HomepagePresenter $p
         */
        $p = $this->getPresenter();
        /**
         * Columns
         */
        $grid->addColumnNumber("id", "Id")
            ->setSortable()
            ->setDefaultHide();

        $grid->addColumnText("name", "Jméno")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["name" => $value]);
            });

        $grid->addColumnText("addressing", "Oslovení")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["addressing" => $value]);
            })
            ->setSortable();

        $grid->addColumnText("company", "Firma")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ['company' => $value]);
            })
            ->setSortable();

        $grid->addColumnText("email", "E-mail")
            ->setEditableCallback(function($id, $value) {
                if(Validators::isEmail($value))
                    $this->invitationsRepository->update($id, ["email" => $value]);
                else
                    $this->presenter->flashMessage("Zadán neplatný email.", 'error');
                    $this->presenter->redirect("this");
            });

        $grid->addColumnNumber('invitation_count', 'Pozvaných')
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["invitation_count" => $value]);
            })
            ->setDefaultHide();

        $grid->addColumnNumber("is_sent", "Odesláno")
            ->setReplacement($is_sent)
            ->setFilterSelect($is_sent);

        $grid->addColumnNumber("is_answered", "Odpověď")
            ->setReplacement($is_answered)
            ->setFilterSelect($is_answered);

        $grid->addColumnNumber('ticket_count', 'Potvrz.')
            ->setFilterMultiSelect($ticket_count);

        $grid->addColumnText("note", "Poznámka zákazníka");

        $grid->addColumnDateTime("reply_deadline", "Termín odpovědi")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ['reply_deadline' => DateTime::createFromFormat('j. n. Y', $value)]);
            })
            ->setFormat('j. n. Y');

        $grid->addColumnText("is_woman","Pohlaví")
            ->setEditableInputTypeSelect($this->sex)
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["is_woman" => $value]);
            })
            ->setReplacement($this->sex)
            ->setDefaultHide();

        $grid->addColumnText("language", "Jazyk")
            ->setEditableInputTypeSelect($this->language)
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["language" => $value]);
            })
            ->setDefaultHide();

        $grid->addColumnText("hash", "url")
            ->setDefaultHide();

        $grid->addColumnText("user_note", "Naše poznámka")
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["user_note" => $value]);
            });

        $grid->addColumnDateTime('invitation_sent_log', 'LogPozvánka')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate()
            ->setCondition(function ($customer, $value) {
                /* @var Selection $a*/
                if ($value == "NULL") {
                    $customer->where("invitation_sent_log IS NULL");
                } else {
                    $customer->where("invitation_sent_log IS NOT NULL");
                }
            });
        $grid->addColumnDateTime('answer_log', 'LogOdpověď')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate()
            ->setCondition(function ($customer, $value) {
                /* @var Selection $a*/
                if ($value == "NULL") {
                    $customer->where("answer_log IS NULL");
                } else {
                    $customer->where("answer_log IS NOT NULL");
                }
            });
        $grid->addColumnDateTime('reminder_sent_log', 'LogUpomínka')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate()
            ->setCondition(function ($customer, $value) {
                /* @var Selection $a*/
                if ($value == "NULL") {
                    $customer->where("reminder_sent_log IS NULL");
                } else {
                    $customer->where("reminder_sent_log IS NOT NULL");
                }
            });
        $grid->addColumnDateTime('confirmation_sent_log', 'LogPotvrzení')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate()
            ->setCondition(function ($customer, $value) {
                /* @var Selection $a*/
                if ($value == "NULL") {
                    $customer->where("confirmation_sent_log IS NULL");
                } else {
                    $customer->where("confirmation_sent_log IS NOT NULL");
                }
            });
        $grid->addColumnDateTime('ticket_sent_log', 'LogVstupenky')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate()
            ->setCondition(function ($customer, $value) {
                /* @var Selection $a*/
                if ($value == "NULL") {
                    $customer->where("ticket_sent_log IS NULL");
                } else {
                    $customer->where("ticket_sent_log IS NOT NULL");
                }
            });
        $grid->addColumnNumber('participated', 'Účast')
            ->setDefaultHide()
            ->setEditableInputTypeSelect([0 => 'Ne', 1 => 'Ano'])
            ->setSortable()
            ->setEditableCallback(function($id, $value) {
                $this->invitationsRepository->update($id, ["participated" => $value]);
            })
            ->setReplacement($participated)
            ->setFilterSelect($participated);

        /*
         * Group Actions
         */
        $grid->addGroupAction('vygenerovat pozvánku (PDF)')->onSelect[] = [$this, 'generateInvitationPDFs'];
        $grid->addGroupAction('vygenerovat vstupenku (PDF)')->onSelect[] = [$this, 'generateTicketPDFs'];

        $grid->addGroupAction('odeslat pozvánku')->onSelect[] = (function ($ids){
            $this->sendMail($ids, $emailTemplate = 'invitation');
        });

        $grid->addGroupAction('odeslat upomínku')->onSelect[] = (function ($ids){
            $this->sendMail($ids, $emailTemplate = 'reminder');
        });

        $grid->addGroupAction('odeslat potvrzení účasti')->onSelect[] = [$this, 'sendConfirmationMail'];

        $grid->addGroupAction('vygenerovat QR kód a odeslat vstupenky')->onSelect[] = [$this, 'sendTicketMail'];

        $grid->addGroupAction('(LogPozvánka)')->onSelect[] = [$this, 'generateLogInvitation'];
        $grid->addGroupAction('(LogOdpověď)')->onSelect[] = [$this, 'generateLogAnswer'];
        $grid->addGroupAction('(LogUpomínka)')->onSelect[] = [$this, 'generateLogReminder'];
        $grid->addGroupAction('(LogPotvrzení)')->onSelect[] = [$this, 'generateLogConfirmation'];
        $grid->addGroupAction('(LogVstupenky)')->onSelect[] = [$this, 'generateLogTicket'];
        $grid->addGroupAction('Označit - zúčastnil/a se')->onSelect[] = [$this, 'generateParticipatedFlag'];

        /*
         * Inline Add
         */
        $grid->addInlineAdd()
            ->onControlAdd[] = function(Container $container){
            $container->addText('name');
            $container->addText('addressing');
            $container->addText('company');

            $container->addText('email');
                //->setRequired('Zadejte email')
                //->setEmptyValue('@')
                //->addRule(Form::MAX_LENGTH, 'Maximální délka emailu je %d znaků', 30)
                //->addRule(Form::EMAIL, 'Zadán neplatný email.');

            $container->addInteger('invitation_count');

            $container->addText('reply_deadline', '')
                ->setType('date');

            $container->addSelect('is_woman', '', $this->sex);
            $container->addSelect('language', '', $this->language);
            //$container->addText('user_note');
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

/*
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
*/
        $grid->setColumnsSummary(['invitation_count','ticket_count', 'is_sent','is_answered','participated']);

        return $grid;
    }

    public function sendMail($ids, $emailTemplate){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        if ($emailTemplate == 'invitation')
            Debugger::log('ODESLÁNÍ POZVÁNEK ZÁKAZNÍKŮM====================; ID ');
        else if ($emailTemplate == 'reminder')
            Debugger::log('ODESLÁNÍ UPOMÍNEK ZÁKAZNÍKŮM====================; ID ');

        $sentInvitationCount = 0;
        $date = date("Y");

        foreach ($customers as $customer) {
            /* @var Customer $customer */
            $mail = new Message;
            $template = parent::createTemplate();
            $template->customer = $customer;
            //Debugger::log('ODESÍLÁNÍ [' . $customer->id . '] [' . $customer->email . '] [' . $customer->name . '] [' . $customer->company . ']; ID ');
            if ($customer->language == 'cz'){
                $subject = "Vánoční večírek $date";
                $attachement = 'pozvánka';
                $attachementName = "Vánoční večírek $date";
            }
            else {
                $subject = "Christmas party $date";
                $attachement = 'invitation';
                $attachementName = "Christmas party $date";
            }
            if ($emailTemplate == 'reminder' && $customer->language == 'cz'){
                $subject = "UPOMÍNKA potvrzení účasti – Vánoční večírek $date";
                $attachement = 'pozvánka';
            }
            //todo en reminder

            $mail->setSubject($subject);
            $mail->setFrom('monika.drobna86@gmail.com', 'Ing. Lukáš Horn');
            $mail->addAttachment("$attachementName - $attachement.pdf", file_get_contents(__INVITATIONS_DIR__."/" . $date . "/" . $customer->id . ".pdf"));
            $template->setFile(__MAIL_DIR__ . '/Generate/' . $emailTemplate . '_' . $customer->language . '.latte');
            $mail->setHtmlBody($template);

            Debugger::log('OK    Připojena příloha [' . $customer->id . '] ' . $customer->email . '; ID ');

            try {
                $mail->addTo($customer["email"]);
                try {
                    $this->mailer->smtpMailer->send($mail);
                } catch (SendException $e) {
                    Debugger::log($e, 'mailexception');
                    Debugger::log('ERROR 1 Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');

                }

                if ($emailTemplate == 'invitation'){
                    $this->invitationsRepository->update($customer->id, ["is_sent" => 1]);
                    $this->invitationsRepository->update($customer->id, ['invitation_sent_log' => new \DateTime()]);
                }
                else if ($emailTemplate == 'reminder'){
                    $this->invitationsRepository->update($customer->id, ['reminder_sent_log' => new \DateTime()]);
                }
                Debugger::log('OK    Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . 'proběhlo v pořádku; ID ');
                $sentInvitationCount++;
            } catch (\Exception $e) {
                $this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
                Debugger::log('ERROR 2 Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');
            }

        }
        $this->presenter->flashMessage("Dokončeno. Odesláno $sentInvitationCount emailů.", 'success');
        $this->presenter->redirect("this");
    }

    public function sendConfirmationMail($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
            Debugger::log('ODESLÁNÍ POTVRZENÍ ÚČASTI====================; ID ');

        $sentInvitationCount = 0;
        $date = date("Y");

        foreach ($customers as $customer) {
            /* @var Customer $customer */
            $mail = new Message;
            $template = parent::createTemplate();
            $template->customer = $customer;
            //Debugger::log('ODESÍLÁNÍ [' . $customer->id . '] [' . $customer->email . '] [' . $customer->name . '] [' . $customer->company . ']; ID ');

            if ($customer->language == 'cz'){
                $subject = "Vánoční večírek $date - potvrzení účasti";
            }
            else {
                $subject = "Christmas party $date - confirmation of participation";
            }
            $mail->setSubject($subject);
            $mail->setFrom('lukas.horn@titan-multiplast.cz', 'Ing. Lukáš Horn');
            $template->setFile(__MAIL_DIR__ . '/Generate/confirmation_' . $customer->language . '.latte');
            $mail->setHtmlBody($template);

            try {
                $mail->addTo($customer["email"]);
                try {
                    $this->mailer->smtpMailer->send($mail);
                } catch (SendException $e) {
                    Debugger::log($e, 'mailexception');
                }

                $this->invitationsRepository->update($customer->id, ['confirmation_sent_log' => new \DateTime()]);
                Debugger::log('OK    Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . 'proběhlo v pořádku; ID ');
                $sentInvitationCount++;
            } catch (\Exception $e) {
                $this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
                Debugger::log('ERROR Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');
            }
        }
        $this->presenter->flashMessage("Dokončeno. Odesláno $sentInvitationCount emailů o potvrzení účasti.", 'success');
        $this->presenter->redirect("this");
    }

    public function sendTicketMail($ids){

        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        Debugger::log('ODESLÁNÍ VSTUPENEK ZÁKAZNÍKŮM====================; ID ');

        $sentTicketCount = 0;
        $date = date("Y");

        if (!is_dir(__QRCODES_DIR__."/" . $date . "/")) {
            mkdir(__QRCODES_DIR__."/" . $date . "/", 0777, true);
        }

        foreach ($customers as $customer) {
            /* @var Customer $customer */

            $qrCode = new QrCode($customer->hash);
            $qrCode->setSize(300);
            $qrCode->writeFile(__QRCODES_DIR__.'/' . $date . '/' . $customer->hash . '.png');

            $mail = new Message;
            $template = parent::createTemplate();
            $template->customer = $customer;
            //Debugger::log('ODESÍLÁNÍ [' . $customer->id . '] [' . $customer->email . '] [' . $customer->name . '] [' . $customer->company . ']; ID ');
            if ($customer->language == 'cz'){
                $subject = "Vánoční večírek $date - vstupenky";
                $attachement = 'elektronická vstupenka';
            }
            else {
                $subject = "Christmas party $date - tickets";
                $attachement = 'e-ticket';
            }
            $mail->setSubject($subject);
            $mail->setFrom('lukas.horn@titan-multiplast.cz', 'Ing. Lukáš Horn');
            $mail->addAttachment("$subject - $attachement.pdf", file_get_contents(__TICKETS_DIR__."/" . $date . "/" . $customer->id . ".pdf"));
            $template->setFile(__MAIL_DIR__ . '/Generate/ticket_' . $customer->language . '.latte');
            $mail->setHtmlBody($template);
            Debugger::log('OK    Připojena příloha [' . $customer->id . '] ' . $customer->email . '; ID ');

            try {
                $mail->addTo($customer["email"]);
                try {
                    $this->mailer->smtpMailer->send($mail);
                } catch (SendException $e) {
                    Debugger::log($e, 'mailexception');
                }
                $this->invitationsRepository->update($customer->id, ['ticket_sent_log' => new \DateTime()]);
                Debugger::log('OK    Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . 'proběhlo v pořádku; ID ');

                $sentTicketCount++;
            } catch (\Exception $e) {
                $this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
                Debugger::log('ERROR Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');
            }

        }
        $this->presenter->flashMessage("Dokončeno. Odesláno $sentTicketCount emailů.", 'success');
        $this->presenter->redirect("this");
    }

    public function generateInvitationPDFs($ids)
    {
        /* @var HomepagePresenter $presenter*/
        $presenter = $this->presenter;
        $presenter->handleGenerateInvitationPdf($ids);
        $this->presenter->flashMessage("Pozvánky (PDF) úspěšně vygenerovány.", "success");
        $this->presenter->redirect("this");
    }

    public function generateTicketPDFs($ids)
    {
        //TODO ošetřit vstupy - potvrzená účast
        /* @var HomepagePresenter $presenter*/
        $presenter = $this->presenter;
        $presenter->handleGenerateTicketPdf($ids);
        $this->presenter->flashMessage("Vstupenky (PDF) úspěšně vygenerovány.", "success");
        $this->presenter->redirect("this");
    }

    public function generateLogInvitation($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['invitation_sent_log' => new \DateTime()]);
        }
        $this->presenter->redirect("this");
    }

    public function generateLogAnswer($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['answer_log' => new \DateTime()]);
        }
        $this->presenter->redirect("this");
    }

    public function generateLogReminder($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['reminder_sent_log' => new \DateTime()]);
        }
        $this->presenter->redirect("this");
    }

    public function generateLogConfirmation($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['confirmation_sent_log' => new \DateTime()]);
        }
        $this->presenter->redirect("this");
    }

    public function generateLogTicket($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['ticket_sent_log' => new \DateTime()]);
        }
        $this->presenter->redirect("this");
    }

    public function generateParticipatedFlag($ids)
    {
        $customers = $this->invitationsRepository->findAll()->where("id", $ids)->fetchAll();
        foreach ($customers as $customer) {
            $this->invitationsRepository->update($customer->id, ['participated' => 1]);
        }
        $this->presenter->redirect("this");
    }
}
