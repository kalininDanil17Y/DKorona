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
		return $this->json;
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
	 * Send the HTTP response to the client.
	 */
	public function send(): void
	{
		header('charset=utf-8');
		http_response_code($this->http_code);
		if (!empty((array) $this->json->get())) {
			header("Content-Type: application/json");
			echo json_encode($this->json->get());
		} else {
			header('Content-Type: text/html;');
			echo $this->body->get();
		}
	}
}
