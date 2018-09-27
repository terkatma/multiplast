<?php

namespace App\Components;

/**
 * Interface IInvitationAnswerComponentFactory
 * @package App\Components
 */
interface IInvitationAnswerComponentFactory
{
    /**
     * @return InvitationAnswerComponent
     */
	public function create($customerId);
}
