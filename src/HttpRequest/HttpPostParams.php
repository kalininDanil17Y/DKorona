<?php
namespace DKLittleSite\HttpRequest;

use DKLittleSite\Abstract\AbstractHttp;

class HttpPostParams extends AbstractHttp
{
	public function __construct()
	{
		if (!empty($_POST)) {
			$this->params = $_POST;
		}
	}
}
