<?php
namespace DKorona\Exceptions;

use DKorona\Abstract\AbstractException;

class PageNotFoundException extends AbstractException
{
	public const NAME = 'PageNotFoundError';

	public function __construct(array $data = [], $message = "Страница не найдена", $code = 404, \Throwable $previous = null)
	{
		parent::__construct($message, $data, $code, $previous);
	}
}
