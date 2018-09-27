<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;


        $router[] = new Route('invitation-answer[/<id>]', [
            'presenter' => 'InvitationAnswer',
            'action' => 'default',
            'id' => null
        ]);

        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
