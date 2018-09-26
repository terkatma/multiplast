<?php

namespace App\Components;

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


    public function __construct(
        InvitationsRepository $invitationsRepository
    )
    {
        parent::__construct();

        $this->invitationsRepository = $invitationsRepository;
    }

    /**
     * @param string $name
     * @return DataGrid
     */
    protected function createComponentInvitationAnswer()
    {

        $invitation_count = [
            '2' => 'Zúčastním se (2 vstupenky)',
            '1' => 'Zúčastním se (1 vstupenka)',
            '0' => 'Nezúčastním se (0 vstupenek)',
        ];

        $form = new Form();

        $form->addSelect('invitation_count', 'Účast a počet vstupenek', $invitation_count)
            ->setPrompt('Vyberte z nabídky')
            ->addRule(Form::FILLED, 'Vyberte počet vstupenek.');

        $form->addSubmit('send', "Odeslat odpověď") //potvrdit účast?
            ->setAttribute('class', 'btn');

        //$form->onSuccess[] = [$this, "signInSubmitted"];
        return $form;
    }

}
