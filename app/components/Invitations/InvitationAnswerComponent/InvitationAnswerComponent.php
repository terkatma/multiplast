<?php

namespace App\Components;

use app\entities\Customer;
use DB\InvitationsRepository;
use Nette\Application\AbortException;
use Nette\Mail\SendException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\Mail\Message;
/**
 * Class InvitationAnswerComponent
 * @package App\Components
 */
class InvitationAnswerComponent extends BaseGridComponent
{

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    protected $invitationsRepository;
    private $customerId;

    /**
     * @inject
     * @var \Utils\Email\Email
     */
    public $mailer;
    private $language;

    public function __construct(
        InvitationsRepository $invitationsRepository, $customerId
    )
    {
        parent::__construct();

        $this->customerId = $customerId;
        $this->invitationsRepository = $invitationsRepository;
    }

    /**
     * @return Form
     */
    protected function createComponentInvitationAnswer()
    {
        /* @var Customer $customer*/
        $customer = $this->invitationsRepository->findById($this->customerId);
        $form = new Form();
        if ($customer->language == 'cz'){
            $invitation_count = [
                '2' => 'Zúčastním se (2 vstupenky)',
                '1' => 'Zúčastním se (1 vstupenka)',
                '0' => 'Nezúčastním se',
            ];
            $label_choose = 'Vyberte';
            $label_note = 'Poznámka';
            $label_send = 'Odeslat odpověď';
        }
        else {
            $invitation_count = [
                '2' => 'Confirm participation (2 tickets)',
                '1' => 'Confirm participation (1 ticket)',
                '0' => 'Sorry, I won\'t be able to attend',
            ];
            $label_choose = 'Choose';
            $label_note = 'Note';
            $label_send = 'Send';
        }
        $form->addRadioList('ticket_count', $label_choose, $invitation_count);
        $form->addTextArea('note', $label_note, 40, 5);
        $form->addSubmit('send', $label_send)
            ->setAttribute('class', 'btn');
        $form->setDefaults($customer);
        $form->onSuccess[] = [$this, "invitationAnswerSubmitted"];
        return $form;
    }

    public function sendMailResponseSaved($id)
    {

        /* @var Customer $customer */
        $customer = $this->invitationsRepository->findById($id);

        Debugger::log('ODESLÁNÍ INFORMACE O ZAZNAMENANÉ ODPOVĚDI5====================; ID ' . $customer->language);

        $date = date("Y");

        $mail = new Message;
        $template = parent::createTemplate();
        $template->customer = $customer;
        if ($customer->ticket_count != 0) {
            $emailTemplate = 'confirmation';
            $subject = $customer->language == 'cz'?"Vánoční večírek $date - potvrzení účasti":"Christmas party $date - confirmation of participation";
        }
        else {
            $emailTemplate = 'responseSaved';
            $subject = $customer->language == 'cz'?"Vánoční večírek $date – vaše odpověď byla zaznamenána":"Christmas party $date – your response has been saved";
        }

        $mail->setSubject($subject);
        $mail->setFrom('monika.drobna86@gmail.com', 'Ing. Lukáš Horn');

        $template->setFile(__MAIL_DIR__ . '/Generate/'. $emailTemplate .'_' . $customer->language . '.latte');
        $mail->setHtmlBody($template);

        try {
            $mail->addTo($customer["email"]);
            try {
                $this->mailer->smtpMailer->send($mail);
            } catch (SendException $e) {
                Debugger::log($e, 'mailexception');
            }
            Debugger::log('OK    Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . 'proběhlo v pořádku; ID ');

        } catch (\Exception $e) {
            //$this->presenter->flashMessage("Mail zákazníkovi se nepodařilo odeslat. [$customer->id] [$customer->email] [$customer->name] [$customer->company]", 'error');
            Debugger::log('ERROR Odeslání mailu zákazníkovi [' . $customer->id . '] ' . $customer->email . ' se nezdařilo; ID ');
        }

        try {
            $this->presenter->redirect("this");
        } catch (AbortException $e) {
        }
    }

    public function invitationAnswerSubmitted(Form $form)
    {
        $values = $form->getValues();
        $this->invitationsRepository->updateCustomer($this->customerId, $values->ticket_count, $values->note);
        $this->invitationsRepository->update($this->customerId, ["is_answered" => 1]);
        $this->invitationsRepository->update($this->customerId, ['answer_log' => new \DateTime()]);
        $this->sendMailResponseSaved($this->customerId);
        $message = $this->customerId->language == 'cz'?'Uloženo':'Save';
        $this->getPresenter()->flashMessage($message, 'success');
    }

}
