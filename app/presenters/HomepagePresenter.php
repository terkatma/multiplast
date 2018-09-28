<?php

namespace App\Presenters;

use App\Components\IInvitationsGridComponentFactory;
use App\Components\InvitationsGridComponent;
use App\Components\ISignInComponentFactory;
use App\Components\SignInComponent;
use Nette;


final class HomepagePresenter extends BaseSecuredPresenter
{
    /**
     * @inject
     * @var ISignInComponentFactory
     */
    public $signInComponentFactory;

    /**
     * @inject
     * @var IInvitationsGridComponentFactory
     */
    public $invitationsGridComponentFactory;

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    public $invitationsRepository;

    /**
     * @return SignInComponent
     */
    public function createComponentSignIn()
    {
        return $this->signInComponentFactory->create();
    }

    /**
     * @return InvitationsGridComponent
     */
    public function createComponentInvitationsGrid()
    {
        return $this->invitationsGridComponentFactory->create();
    }

    public function actionDefault()
    {
        $this->template->invitationCount = $this->invitationsRepository->findAll()->sum("invitation_count");
        $this->template->ticketCount = $this->invitationsRepository->findAll()->sum("ticket_count");
    }


}
