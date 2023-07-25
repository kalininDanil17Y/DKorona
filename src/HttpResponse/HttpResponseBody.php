<?php

namespace DKorona\HttpResponse;

class httpResponseBody
{
	private string $text;

	public function __construct()
	{
		$this->text = "";
	}

	/**
	 * Set the body content for the response.
	 *
	 * @param string $text
	 *   The body content.
	 */
	public function set(string $text): static
	{
		$this->text = $text;
		return $this;
	}

	/**
	 * Add text to the end of the body content.
	 *
	 * @param string $text
	 *   The text to add.
	 */
	public function add(string $text): static
	{
		$this->text .= $text;
		return $this;
	}

	/**
	 * Add text to the beginning of the body content.
	 *
	 * @param string $text
	 *   The text to add.
	 */
	public function prepend(string $text): static
	{
		$this->text = $text . $this->text;
		return $this;
	}

	/**
	 * Get the body content for the response.
	 *
	 * @return string
	 *   The body content.
	 */
	public function get(): string
	{
		return $this->text;
	}
}
