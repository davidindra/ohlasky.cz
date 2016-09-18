<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('kostely', 'Church:list');
		$router[] = new Route('kostel/<church>', 'Church:view');
		$router[] = new Route('prihlasit', 'Homepage:signIn');
		$router[] = new Route('odhlasit', 'Homepage:signOut');
		$router[] = new Route('<presenter>/<action>', 'Homepage:default');
		return $router;
	}

}
