<?php
namespace DKorona\Exceptions;

use DKorona\Abstract\AbstractException;

class BadRequestException extends AbstractException
{
	public const NAME = 'BadRequestError';

	public function __construct(array $data = [], $message = "", $code = 404, \Throwable $previous = null)
	{
		parent::__construct($message, $data, $code, $previous);
	}
}
