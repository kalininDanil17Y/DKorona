<?php
namespace DKLittleSite\Controllers;

use DKLittleSite\DKCore;
use DKLittleSite\Exceptions\ForbiddenException;
use DKLittleSite\Exceptions\PageNotFoundException;
use DKLittleSite\HttpRequest;
use DKLittleSite\httpResponse;
use DKLittleSite\Models\User;
use DKLittleSite\Router;

class HomeController
{
	public function index(HttpRequest $req, HttpResponse $res, array $match, Router $router): void
	{
		echo 'Index page';
		echo '<a href="' . $router->getPath('get_user', ['id' => 2])  . '">User 2</a>';
	}

	/**
	 * @throws PageNotFoundException
	 * @throws ForbiddenException
	 */
	public function user(HttpRequest $req, HttpResponse $res, array $match): void
    {
		$res->setIsJson(true);
		//$id = $req->getGetParams()->get('id', 2);

	    $id = DKCore::arrayGet($match, 'id');

		if (!$id) {
			throw new ForbiddenException();
		}

		$user = User::find($id);

		if (!$user) {
			throw new PageNotFoundException();
		}

	    $res->json()->set([
			'method' => 'getUser',
			'user' => $user->getData(),
		    'GET[id]' => $id
	    ]);
    }
}
