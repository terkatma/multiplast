<?php

namespace App\Presenters;

use App\Components\IInvitationAnswerComponentFactory;
use App\Components\InvitationAnswerComponent;
use App\Components\IInvitationsGridComponentFactory;
use App\Components\InvitationsGridComponent;
use App\Components\ISignInComponentFactory;
use App\Components\SignInComponent;
use http\Exception\BadUrlException;
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

    private $customerId;

    /**
     * @return InvitationAnswerComponent
     */
    public function createComponentInvitationAnswer()
    {
        return $this->invitationAnswerComponentFactory->create($this->customerId);
    }

    public function actionDefault($hash)
    {
        $this->customerId = $this->invitationsRepository->getIdByHash($hash);
        if (!$this->customerId) {
            throw new Nette\Application\BadRequestException();
        }
        $this->template->customer = $this->invitationsRepository->getIdByHash($hash);
    }


}
