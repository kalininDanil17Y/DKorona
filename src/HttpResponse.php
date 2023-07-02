<?php
namespace DKLittleSite;

use DKLittleSite\HttpResponse\HttpResponseBody;
use DKLittleSite\HttpResponse\HttpResponseJson;

/**
 * Class httpResponse represents an HTTP response.
 */
class httpResponse
{
	/**
	 * The HTTP status code for the response.
	 *
	 * @var int
	 */
	private int $http_code;

	/**
	 * @var bool
	 */
	private bool $is_json = false;

	/**
	 * The JSON object for the response.
	 *
	 * @var HttpResponseJson
	 */
	private HttpResponseJson $json;

	/**
	 * The body of the response.
	 *
	 * @var HttpResponseBody
	 */
	private HttpResponseBody $body;

	/**
	 * Creates a new HTTP response instance.
	 */
	public function __construct()
	{
		$this->http_code = 200;
		$this->json = new HttpResponseJson();
		$this->body = new HttpResponseBody();
	}

	/**
	 * Get the httpResponseJson object for the response.
	 *
	 * @return HttpResponseJson
	 *   The JSON object.
	 */
	public function json(): HttpResponseJson
	{
		$this->is_json = true;
		return $this->json;
	}

	/**
	 * @param bool $bool
	 *
	 * @return void
	 */
	public function setIsJson(bool $bool): void
	{
		$this->is_json = $bool;
	}

	/**
	 * @return bool
	 */
	public function isJson(): bool
	{
		return $this->is_json;
	}

	/**
	 * Set the HTTP status code for the response.
	 *
	 * @param int $http_code
	 *   The HTTP status code.
	 */
	public function httpCode(int $http_code): static
	{
		$this->http_code = $http_code;
		return $this;
	}

	/**
	 * Get the httpResponseBody object for the response.
	 *
	 * @return httpResponseBody
	 *   The body object.
	 */
	public function body(): HttpResponseBody
	{
		return $this->body;
	}

	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @return $this
	 */
	public function View(string $template, array $data): static
	{
		$this->body()->set(View::render($template, $data));
		return $this;
	}

	/**
	 * Send the HTTP response to the client.
	 */
	public function send(): void
	{
		http_response_code($this->http_code);
		if ($this->is_json) {
			header("Content-Type: application/json; charset=utf-8;");
			echo json_encode($this->json->get());
		} else {
			header('Content-Type: text/html; charset=utf-8;');
			echo $this->body->get();
		}
	}
}
