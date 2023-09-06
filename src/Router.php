<?php
namespace DKorona;

use DKorona\Abstract\AbstractException;
use DKorona\Exceptions\InternalServerErrorException;
use DKorona\Exceptions\PageNotFoundException;
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

	/**
	 * Добавляет маршрут с определенным URL, контроллером и методом запроса.
	 *
	 * @param $url
	 * @param $controller
	 * @param string $method
	 * @param $regex
	 *
	 * @return $this
	 */
	public function addRoute($url, $controller, string $method = "GET", $regex = null): static
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
			"middleware" => ''
		];
		return $this;
	}

	/**
	 * Устанавливает имя для последнего добавленного маршрута.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function name(string $name): bool
	{
		$id = array_key_last($this->routes);
		if ($id === null) {
			return false;
		}

		$this->routes[$id]['name'] = $name;
		return true;
	}

	/**
	 * Генерирует URL, заменяя параметры в фигурных скобках на соответствующие значения из массива параметров.
	 *
	 * @param string $url
	 * @param array  $params
	 *
	 * @return string
	 */
	private function generate_url(string $url, array $params): string {
		foreach ($params as $key => $value) {
			$url = str_replace("{".$key."}", $value, $url);
		}
		return $url;
	}

	/**
	 * Возвращает массив всех добавленных маршрутов.
	 *
	 * @return array
	 */
	public function getRouters(): array
	{
		return $this->routes;
	}

	/**
	 * Возвращает путь (URL) для маршрута с определенным именем и параметрами.
	 *
	 * @param string $name
	 * @param array  $params
	 *
	 * @return string|false
	 */
	public function getPath(string $name, array $params = []): string|false
	{
		$route = $this->getRoute($name);
		if (!$route) {
			return false;
		}

		return $this->generate_url($route['url'], $params);
	}

	/**
	 * Возвращает информацию о маршруте с определенным именем.
	 * @param string $name
	 *
	 * @return array|false
	 */
	public function getRoute(string $name): array|false
	{
		foreach($this->routes as $route) {
			if ($route['name'] === $name) {
				return $route;
			}
		}

		return false;
	}

	/**
	 * Группирует маршруты и применяет общий префикс для URL.
	 *
	 * @param          $prefix
	 * @param callable $callback
	 * @param string   $groupName
	 * @param string   $middleware
	 *
	 * @return void
	 */
	public function group($prefix, callable $callback, string $groupName = '', string $middleware = ''): void
	{
		$router = new Router(new DKCore());

		// Call the callback with the router instance
		call_user_func($callback, $router);

		// Prepend the prefix to all routes added inside the callback
		foreach ($router->getRouters() as $route) {
			$route['url'] = $prefix . $route['url'];

			$pattern = preg_replace('/\{([^}]+)}/', '(?P<$1>[^/]+)', $route['url']);
			$route['pattern'] = "/^" . str_replace("/", "\/", $pattern) . "$/";

			$route['name'] = $groupName . $route['name'];
			$route['middleware'] = ($route['middleware'] !== '') ? $route['middleware'] : $middleware;

			$this->routes[] = $route;
		}
	}

	/**
	 * Пре-контроллер роутера
	 *
	 * @param string $middleware
	 *
	 * @return bool
	 */
	public function middleware(string $middleware): bool
	{
		$id = array_key_last($this->routes);
		if ($id === null) {
			return false;
		}

		$this->routes[$id]['middleware'] = $middleware;
		return true;
	}

	/**
	 * Обрабатывает входящий запрос, находит соответствующий маршрут и вызывает соответствующий контроллер для обработки запроса.
	 *
	 * @param $url
	 * @param $method
	 *
	 * @return void
	 */
	public function route($url = null, $method = null): void
	{
		$request = new HttpRequest();
		$response = new HttpResponse();

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
							$class = '\DKorona\Controllers\\' . $class;
						}

						if (!class_exists($class)) {
							throw new \Exception("Class $class does not exist");
						}
						if (!method_exists($class, $method)) {
							throw new \Exception("Method $method does not exist in class $class");
						}

						/** @var true $middlewareResponse */
						$middlewareResponse = true;
						if (!empty($route['middleware']) && $route['middleware'] !== '') {
							$middlewareName = $route['middleware'];
                            call_user_func(['\DKorona\Middleware\\' . $middlewareName, 'run'], $request, $response, $matches, $this);
						}

						if ($middlewareResponse) {
							$instance = new $class();
							$instance->$method($request, $response, $matches, $this);
						}
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
	 * Метод для рендеринга ошибок, включая обработку исключений и генерацию соответствующего ответа.
	 *
	 * @param HttpResponse                $response
	 * @param AbstractException|Throwable $error
	 *
	 * @return void
	 */
	private function renderError(HttpResponse $response, AbstractException|Throwable $error): void
	{
		/**
		 * Если это не кастомная ошибка, делаем её 500
		 */
		$array = explode('\\', $error::class);
		$className = end($array);
		$filepath = __DIR__ . '/../src/Exceptions/' . $className . '.php';

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
