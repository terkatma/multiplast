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

    public $ticket_count = [];
    public $all_tickets = [];

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

        $this->ticket_count = $ticket_count = ['' => 'Vyberte', 0 => '0', 1 => '1', 2 => '2'];

        /**
         * Columns
         */
        $grid->addColumnText("name", "Jméno");
        $grid->addColumnText("company", "Firma");
        $grid->addColumnText("email", "E-mail");
        //$grid->addColumnText( "email2", "E-mail", "email");
        //$grid->addColumnNumber("ticket_count", "Počet lístků");
        $grid->addColumnText('ticket_count', 'Počet lístků')->setFilterSelect($this->ticket_count);
        $grid->addColumnText("note", "Poznámka");

        return $grid;
    }
}
