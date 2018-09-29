<?php

namespace App\Presenters;

use App\Components\IInvitationsGridComponentFactory;
use App\Components\IListImportComponentFactory;
use App\Components\InvitationsGridComponent;
use App\Components\ISignInComponentFactory;
use App\Components\ListImportComponent;
use App\Components\SignInComponent;
use app\entities\Customer;


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
     * @inject
     * @var IListImportComponentFactory
     */
    public $listImportComponentFactory;

    /**
     * @inject
     * @var \Utils\PDFExport\PDFExport
     */
    public $pdfExport;

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
        $this->template->isSentCount = $this->invitationsRepository->sumOfSentInvitations();
    }

    public function handleGeneratePdf($ids) {
        /* @var Customer[] $customers */
        $customers = $this->invitationsRepository
            ->findAll()
            ->where("id", $ids)
            ->fetchAll();

        if (!is_dir(__INVITATIONS_DIR__."/" . date("Y") . "/")) {
            mkdir(__INVITATIONS_DIR__."/" . date("Y") . "/", 0777, true);
        }

        foreach ($customers as $customer) {
            $this->pdfExport->generateInvitationPdf($customer);
        }
    }



    /**
     * @return ListImportComponent
     */
    public function createComponentListImport()
    {
        return $this->listImportComponentFactory->create();
    }
}
