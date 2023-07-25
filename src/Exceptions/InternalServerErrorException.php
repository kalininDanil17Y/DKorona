<?php
namespace DKorona\Exceptions;

use DKorona\Abstract\AbstractException;

class InternalServerErrorException extends AbstractException
{
	public const NAME = 'InternalServerError';

	public function __construct(array $data = [], $message = "Внутренняя ошибка сервера", $code = 500, \Throwable $previous = null)
	{
		parent::__construct($message, $data, $code, $previous);
	}
}
