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
	}

	public function setupRouters(callable $func): void
	{
		$func($this->router, $this);
		$this->router->route();
	}
}
