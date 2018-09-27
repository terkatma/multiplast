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

        $invitation_count = [
            '2' => 'Zúčastním se (2 vstupenky)',
            '1' => 'Zúčastním se (1 vstupenka)',
            '0' => 'Nezúčastním se (0 vstupenek)',
        ];

        $form = new Form();

        $form->addSelect('ticket_count', 'Účast a počet vstupenek', $invitation_count)
            ->setPrompt('Vyberte z nabídky');

        $form->addSubmit('send', "Odeslat odpověď") //potvrdit účast?
            ->setAttribute('class', 'btn');

        $form->setDefaults($customer);

        $form->onSuccess[] = [$this, "invitationAnswerSubmitted"];
        return $form;
    }

    public function invitationAnswerSubmitted(Form $form)
    {
        $values = $form->getValues();
        $this->invitationsRepository->updateCustomer($this->customerId, $values->ticket_count);
        $this->getPresenter()->flashMessage('Uloženo', 'success');
    }

}
