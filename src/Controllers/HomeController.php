<?php
namespace DKLittleSite\Controllers;

use DKLittleSite\HttpRequest;
use DKLittleSite\httpResponse;
use DKLittleSite\Models\User;

class HomeController
{
    public function index(HttpRequest $req, HttpResponse $res): void
    {
		$id = $req->getGetParams()->get('id', 2);
		$user = User::find($id);
	    $res->json()->set([
			'method' => 'getUser',
			'user' => $user ? $user->getData() : 'User not loaded',
		    'GET[id]' => $id
	    ]);
    }
}
