<?php
namespace DKorona\HttpRequest;

use DKorona\Abstract\AbstractHttp;

class HttpPostParams extends AbstractHttp
{
	public function __construct()
	{
		if (!empty($_POST)) {
			$this->params = $_POST;
		}
	}
}
