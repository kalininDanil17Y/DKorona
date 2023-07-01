<?php
namespace DKLittleSite\HttpRequest;

use DKLittleSite\Abstract\AbstractHttp;

class HttpGetParams extends AbstractHttp
{
	public function __construct()
	{
		if (!empty($_GET)) {
			$this->params = $_GET;
		}
	}
}
