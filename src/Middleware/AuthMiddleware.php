<?php
namespace DKorona\Middleware;

use DKorona\Exceptions\ForbiddenException;
use DKorona\HttpRequest;
use DKorona\httpResponse;
use DKorona\Router;
use Throwable;

class AuthMiddleware
{
	/**
	 * @throws Throwable
	 */
	static function run(HttpRequest $req, HttpResponse $res, array $match, Router $router): void
	{
		throw new ForbiddenException();
	}
}
