<?php

namespace DKLittleSite;

class DKCore
{
	private Router $router;
	public Logger $logger;
	public SystemVar $system_var;

	public function __construct()
	{
		$this->router = new Router($this);
		$this->system_var = new SystemVar();

		$this->system_var->load('main');

		$this->logger = new Logger(
			$this->system_var->get('coreLogFileName'),
			$this->system_var->get('coreLogDetail')
		);

		if ($this->system_var->get('debug')) {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);
		}
	}

	public function setupRouters(callable $func): void
	{
		$func($this->router, $this);
		$this->router->route();
	}

	public static function arrget(array $arr, string|int $key)
	{
		return array_key_exists($key, $arr) ? $arr[$key] : null;
	}
}
