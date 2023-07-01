<?php
namespace DKLittleSite;

use DKLittleSite\HttpRequest\HttpGetParams;
use DKLittleSite\HttpRequest\HttpPostParams;
use DKLittleSite\HttpRequest\HttpServer;

class HttpRequest
{
	private HttpServer $server;
	private HttpGetParams $getParams;
	private HttpPostParams $postParams;

	public function __construct()
	{
		$this->server = new HttpServer();
		$this->getParams = new HttpGetParams();
		$this->postParams = new HttpPostParams();
	}

	public function getServer(): HttpServer
	{
		return $this->server;
	}

	public function getGetParams(): HttpGetParams
	{
		return $this->getParams;
	}

	public function getPostParams(): HttpPostParams
	{
		return $this->postParams;
	}

	public function getRequestMethod(): string
	{
		return $this->getServer()->get('REQUEST_METHOD', 'GET');
	}

	public function getRequestUrl(): string
	{
		return $this->getServer()->get('REQUEST_URI', '/');
	}

	public function __toJson(): bool|string
	{
		return json_encode([
			"server" => $this->server->getAll(),
			"getParams" => $this->getParams->getAll(),
			"postParams" => $this->postParams->getAll(),
		]);
	}

	public function __toArray(): array
	{
		return [
			"server" => $this->server->getAll(),
			"getParams" => $this->getParams->getAll(),
			"postParams" => $this->postParams->getAll(),
		];
	}
}
