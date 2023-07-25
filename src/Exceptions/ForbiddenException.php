<?php
namespace DKorona\Exceptions;

use DKorona\Abstract\AbstractException;

class ForbiddenException extends AbstractException
{
	public const NAME = 'ForbiddenError';

	public function __construct(array $data = [], string $message = "Доступ запрещен", int $code = 403, \Throwable $previous = null)
	{
		parent::__construct($message, $data, $code, $previous);
	}
}
