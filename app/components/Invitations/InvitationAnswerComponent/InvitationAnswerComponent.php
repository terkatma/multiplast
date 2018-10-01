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
                '0' => 'Refuse',
            ];
            $form->addRadioList('ticket_count', 'Choose', $invitation_count);
            $form->addTextArea('note', 'Note', 40, 5);
            $form->addSubmit('send', "Send")
                ->setAttribute('class', 'btn');
        }
        else {
            $invitation_count = [
                '2' => 'Zúčastním se (2 vstupenky)',
                '1' => 'Zúčastním se (1 vstupenka)',
                '0' => 'Nezúčastním se',
            ];
            $form->addRadioList('ticket_count', 'Vyberte', $invitation_count);
            //->setPrompt('Vyberte z nabídky');
            $form->addTextArea('note', 'Poznámka', 40, 5);
            // ->addRule(Form::FILLED, 'Zadejte popis.')
            // ->addRule(Form::MAX_LENGTH, 'Poznámka je příliš dlouhá', 10000);
            $form->addSubmit('send', "Odeslat odpověď")
                ->setAttribute('class', 'btn');
        }

        $form->setDefaults($customer);
        $form->onSuccess[] = [$this, "invitationAnswerSubmitted"];
        return $form;
    }

    public function invitationAnswerSubmitted(Form $form)
    {
        $values = $form->getValues();
        $this->invitationsRepository->updateCustomer($this->customerId, $values->ticket_count, $values->note);
        $this->invitationsRepository->updateCustomerIsAnswered($this->customerId->id, 1);
        $this->getPresenter()->flashMessage('Uloženo', 'success' );
    }

}
