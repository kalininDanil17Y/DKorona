<?php
namespace DKLittleSite;

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
				} catch (\Throwable $e) {
					if ($this->core->system_var->get('debug')) {
						$response->json()->set([
							'error' => $e->getMessage(),
							'file' => $e->getFile(),
							'line' => $e->getLine()
						]);
					} else {
						$response->httpCode(500)->body()->set('Server error');
					}
				}

				$response->send();
				return;
			}
		}

		// Если страницы нет, вывести "404"
		header("HTTP/1.0 404 Not Found");
		echo "404";
	}
}
