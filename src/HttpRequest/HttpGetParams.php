<?php
namespace DKorona\HttpRequest;

use DKorona\Abstract\AbstractHttp;

class HttpGetParams extends AbstractHttp
{
	public function __construct()
	{
		if (!empty($_GET)) {
			$this->params = $_GET;
		}
	}
}
