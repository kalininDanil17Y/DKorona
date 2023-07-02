<?php

namespace DKLittleSite\Abstract;

class AbstractException extends \Exception
{
	private array $vars = [];
	private null|string $customFile = null;
	private null|int $customLine = null;

	public function __construct($message, array $data = [], $code = 404, \Throwable $previous = null)
	{
		$this->vars = $data;

		if (array_key_exists('file', $this->vars)) {
			$this->customFile = $this->vars['file'];
			unset($this->vars['file']);
		}

		if (array_key_exists('line', $this->vars)) {
			$this->customLine = $this->vars['line'];
			unset($this->vars['line']);
		}

		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->vars;
	}

	/**
	 * @return string
	 */
	public function getCustomFile(): string
	{
		return $this->customFile ?: parent::getFile();
	}

	/**
	 * @return string
	 */
	public function getCustomLine(): mixed
	{
		return $this->customLine ?: parent::getLine();
	}
}
