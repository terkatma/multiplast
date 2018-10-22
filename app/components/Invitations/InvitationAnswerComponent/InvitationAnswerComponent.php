<?php

namespace App\Components;

use app\entities\Customer;
use DB\InvitationsRepository;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Form;


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
        if ($customer->language == 'en'){
            $invitation_count = [
                '2' => 'Confirm participation (2 tickets)',
                '1' => 'Confirm participation (1 ticket)',
                '0' => 'Sorry, I won\'t be able to attend',
            ];
            $label_choose = 'Choose';
            $label_note = 'Note';
            $label_send = 'Send';
        }
        else {
            $invitation_count = [
                '2' => 'Zúčastním se (2 vstupenky)',
                '1' => 'Zúčastním se (1 vstupenka)',
                '0' => 'Nezúčastním se',
            ];
            $label_choose = 'Vyberte';
            $label_note = 'Poznámka';
            $label_send = 'Odeslat odpověď';
        }
        $form->addRadioList('ticket_count', $label_choose, $invitation_count);
        $form->addTextArea('note', $label_note, 40, 5);
        $form->addSubmit('send', $label_send)
            ->setAttribute('class', 'btn');
        $form->setDefaults($customer);
        $form->onSuccess[] = [$this, "invitationAnswerSubmitted"];
        return $form;
    }

    public function invitationAnswerSubmitted(Form $form)
    {
        $values = $form->getValues();
        $this->invitationsRepository->updateCustomer($this->customerId, $values->ticket_count, $values->note);
        $this->invitationsRepository->updateCustomerIsAnswered($this->customerId->id, 1);
        $this->invitationsRepository->updateCustomerAnswerLog($this->customerId->id);

        $this->customerId->language == 'en'?$message = 'Save':$message = 'Uloženo';
        $this->getPresenter()->flashMessage($message, 'success');
    }

}
