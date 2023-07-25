<?php

namespace DKorona\Abstract;

abstract class AbstractHttp
{
	protected array $params = [];

	public function get($name, $defaultValue = null): string|array|null
	{
		return $this->params[$name] ?? $defaultValue;
	}

	public function toJson(): string
	{
		return json_encode($this->params);
	}

	public function getAll(): array
	{
		return $this->params;
	}
}
