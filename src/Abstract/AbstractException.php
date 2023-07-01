<?php

namespace DKLittleSite\Abstract;

class AbstractException extends \Exception
{
	private array $vars = [];

	public function __construct($message, array $data = [], $code = 404, \Throwable $previous = null)
	{
		$this->vars = $data;
		parent::__construct($message, $code, $previous);
	}

	public function getData(): array
	{
		return $this->vars;
	}
}
