<?php
namespace DKorona\HttpRequest;

use DKorona\Abstract\AbstractHttp;

class HttpServer extends AbstractHttp
{
	public function __construct()
	{
		if (!empty($_SERVER)) {
			$this->params = $_SERVER;
		}
	}
}
