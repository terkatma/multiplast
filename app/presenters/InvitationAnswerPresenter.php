<?php

namespace App\Presenters;

use App\Components\IInvitationAnswerComponentFactory;
use App\Components\InvitationAnswerComponent;
use App\Components\IInvitationsGridComponentFactory;
use App\Components\InvitationsGridComponent;
use App\Components\ISignInComponentFactory;
use App\Components\SignInComponent;
use Nette;


final class InvitationAnswerPresenter extends Nette\Application\UI\Presenter
{

    /**
     * @inject
     * @var IInvitationAnswerComponentFactory
     */
    public $invitationAnswerComponentFactory;

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    public $invitationsRepository;

    /**
     * @return InvitationAnswerComponent
     */
    public function createComponentInvitationAnswer()
    {
        return $this->invitationAnswerComponentFactory->create();
    }

    public function actionDefault()
    {
    }


}
