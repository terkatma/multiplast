<?php

namespace App\Components;

/**
 * Interface IInvitationsGridComponentFactory
 * @package App\Components
 */
interface IInvitationsGridComponentFactory
{
    /**
     * @return InvitationsGridComponent
     */
	public function create();
}
