<?php
namespace DKLittleSite\Controllers;

use DKLittleSite\DKCore;
use DKLittleSite\Exceptions\ForbiddenException;
use DKLittleSite\Exceptions\PageNotFoundException;
use DKLittleSite\HttpRequest;
use DKLittleSite\httpResponse;
use DKLittleSite\Models\User;

class HomeController
{
	public function index()
	{
		echo 'Index page';
	}

	/**
	 * @throws PageNotFoundException
	 * @throws ForbiddenException
	 */
	public function user(HttpRequest $req, HttpResponse $res, array $match): void
    {
		$res->setIsJson(true);
		//$id = $req->getGetParams()->get('id', 2);

	    $id = DKCore::arrget($match, 'id');

		if (!$id) {
			throw new ForbiddenException();
		}

		1/0;

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
