<?php
namespace DKLittleSite;

use DKLittleSite\Abstract\AbstractException;
use DKLittleSite\Exceptions\InternalServerErrorException;
use DKLittleSite\Exceptions\PageNotFoundException;
use Throwable;

class Router
{
	private array $routes;
	private DKCore $core;

	public function __construct($core)
	{
		$this->routes = [];
		$this->core = $core;
	}

	public function addRoute($url, $controller, $method = "GET", $regex = null): static
	{
		if ($regex) {
			$pattern = $regex;
		} else {
			// Заменяем параметры в {} на соответствующие регулярные выражения
			$pattern = preg_replace('/\{([^}]+)}/', '(?P<$1>[^/]+)', $url);
			$pattern = "/^" . str_replace("/", "\/", $pattern) . "$/";
		}

		$this->routes[] = [
			"url" => $url,
			"pattern" => $pattern,
			"controller" => $controller,
			"method" => $method,
			"name" => '',
		];
		return $this;
	}

	public function name(string $name): bool
	{
		$id = array_key_last($this->routes);
		if ($id === null) {
			return false;
		}

		$this->routes[$id]['name'] = $name;
		return true;
	}

	private function generate_url(string $url, $params): string {
		foreach ($params as $key => $value) {
			$url = str_replace("{".$key."}", $value, $url);
		}
		return $url;
	}

	public function getPath(string $name, $params = []): string|false
	{
		$route = $this->getRoute($name);
		if (!$route) {
			return false;
		}

		return $this->generate_url($route['url'], $params);
	}

	public function getRoute(string $name): array|false
	{
		foreach($this->routes as $route) {
			if ($route['name'] === $name) {
				return $route;
			}
		}

		return false;
	}

	public function group($prefix, callable $callback): void
	{
		$oldRoutes = $this->routes;
		$this->routes = [];

		// Call the callback with the router instance
		call_user_func($callback, $this);

		// Prepend the prefix to all routes added inside the callback
		foreach ($this->routes as $route) {
			$route['url'] = $prefix . $route['url'];
			$this->routes[] = $route;
		}

		// Restore the old routes
		$this->routes = array_merge($oldRoutes, $this->routes);
	}

	public function route($url = null, $method = null): void
	{
		$request = new HttpRequest();
		$response = new httpResponse();

		$response->initViewFunctions($this);

		$url = $url ?? $request->getRequestUrl();
		$method = $method ?? $request->getRequestMethod();

		// Определяем строку GET параметров
		$query_string_pos = strpos($url, '?');
		if ($query_string_pos !== false) {
			$url = substr($url, 0, $query_string_pos);
		}

		foreach ($this->routes as $route) {
			// Если найден маршрут, который соответствует URL и методу запроса
			if (preg_match($route["pattern"], $url, $matches) && $route["method"] == $method) {
				$controller = $route["controller"];

				try {
					// Если контроллер является анонимной функцией, то выполняем ее
					if (is_callable($controller)) {
						call_user_func($controller, $request, $response, $matches, $this);
					} else {
						// Если контроллер является строкой, то создаем экземпляр класса и вызываем его метод
						list($class, $method) = explode("@", $controller);

						$class = str_replace('/', '\\', $class);
						if (!str_contains($class, '\\')) {
							$class = '\DKLittleSite\Controllers\\' . $class;
						}

						if (!class_exists($class)) {
							throw new \Exception("Class $class does not exist");
						}
						if (!method_exists($class, $method)) {
							throw new \Exception("Method $method does not exist in class $class");
						}

						$instance = new $class();
						$instance->$method($request, $response, $matches, $this);
					}
				} catch (Throwable $e) {
					$this->renderError($response, $e);
				}

				$response->send();
				return;
			}
		}

		$this->renderError($response, new PageNotFoundException());
		$response->send();
	}

	/**
	 * @param httpResponse                $response
	 * @param AbstractException|Throwable $error
	 *
	 * @return void
	 */
	private function renderError(httpResponse $response, AbstractException|Throwable $error): void
	{
		/**
		 * Если это не кастомная ошибка, делаем её 500
		 */
		$className = pathinfo($error::class, PATHINFO_BASENAME);
		$filepath = __DIR__ . '/../src/Exceptions/' . $className . '.php';
		print_r($filepath);

		if (!(class_exists($error::class) && file_exists($filepath))) {
			$error = new InternalServerErrorException([
				'file' => $error->getFile(),
				'line' => $error->getLine()
			], $error->getMessage());
		}

		/**
		 * Ставим код ответа http
		 */
		$response->httpCode($error->getCode());

		$data = [
			'title' => $error->getMessage(),
			'error' => $error->getMessage(),
			'type' => $error::NAME,
			'code' => $error->getCode(),
			'data' => $error->getData(),
			'debug' => $this->core->system_var->get('debug')
		];

		if ($data['debug']) {
			$data['file'] = $error->getCustomFile();
			$data['line'] =$error->getCustomLine();
		}

		/**
		 * Рендерим json
		 */
		if ($response->isJson()) {
			$response->json()->set($data);
			return;
		}

		/**
		 * Рендерим html
		 */
		$response->View($error::NAME, $data);
	}
}
