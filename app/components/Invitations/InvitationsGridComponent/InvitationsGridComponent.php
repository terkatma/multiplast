<?php

namespace App\Components;

use app\entities\Generate;
use app\entities\Team;
use Ublaboo\DataGrid\DataGrid;


/**
 * Class InvitationsGridComponent
 * @package App\Components
 */
class InvitationsGridComponent extends BaseGridComponent
{

    /**
     * @inject
     * @var \DB\InvitationsRepository
     */
    public $invitationsRepository;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     * @return DataGrid
     */
    public function createComponentInvitationsGrid($name)
    {

        /**
         * @var DataGrid $grid
         */
        $grid = $this->getGrid($name);
        $grid->setDataSource($this->invitationsRepository->findAll());

        /**
         * Columns
         */
        $grid->addColumnText("name", "Jméno");
        $grid->addColumnText("company", "Firma");
        $grid->addColumnText("email", "E-mail");

        return $grid;
    }
}
