<?php

namespace DKLittleSite\HttpResponse;

class HttpResponseJson
{
	private array $data;

	public function __construct()
	{
		$this->data = [];
	}

	/**
	 * Set a JSON.
	 */
	public function set(array $data): static
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Get the JSON data object for the response.
	 *
	 * @return array
	 *   The JSON data object.
	 */
	public function get(): array
	{
		return $this->data;
	}

	/**
	 * Add a property to the JSON object.
	 *
	 * @param string $key
	 *   The name of the property.
	 * @param mixed  $value
	 *   The value of the property.
	 */
	public function add(string $key, mixed $value): static
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Delete a property from the JSON object.
	 *
	 * @param string $key
	 *   The name of the property to delete.
	 */
	public function del(string $key): static
	{
		unset($this->data[$key]);
		return $this;
	}
}
