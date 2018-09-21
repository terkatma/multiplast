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
}
