<?php
namespace DKLittleSite\Exceptions;

use DKLittleSite\Abstract\AbstractException;

class InternalServerErrorException extends AbstractException
{
	public function __construct(array $data = [], $message = "Внутренняя ошибка сервера", $code = 500, \Throwable $previous = null)
	{
		parent::__construct($message, $data, $code, $previous);
	}
}
