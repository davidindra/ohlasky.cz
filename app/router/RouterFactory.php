<?php

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use App\Model\Repository\Churches;

class RouterFactory
{
	private $churches;

	public function __construct(Churches $churches) {
		$this->churches = $churches;
	}

	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('[kostel/]<church>', [
			'presenter' => 'Church',
			'action' => 'default',
			'church' => [
				Route::FILTER_STRICT => true,
				Route::FILTER_TABLE => $this->churchesFilterTable()
			]
		]);

		//$router[] = new Route('prihlasit', 'Homepage:signIn'); // TODO presunout login form na jine misto atd.?

		$router[] = new Route('<presenter>[/<action>]', [
			'presenter' => [
				Route::VALUE => 'Homepage',
				Route::FILTER_TABLE => [
					'kostely' => 'ChurchList',
					'kostel' => 'Church',
					'mse' => 'Mass',
					'oprojektu' => 'About',
					'ucet' => 'Account',
					'export' => 'Export',
					'tisk' => 'Print',
					'admin' => 'Admin'
				],
			],
			'action' => 'default'
		]);

		return $router;
	}

	private function churchesFilterTable(){
		$table = [];
		foreach($this->churches->getAll() as $church){
			$table[$church->abbreviation] = $church->abbreviation;
		}
		return $table;
	}
}
