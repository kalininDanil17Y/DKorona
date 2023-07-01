<?php
require_once __DIR__ . '/../my_autoload.php';

use DKLittleSite\DKCore;
use DKLittleSite\Router;

$core = new DKCore();

$core->setupRouters(function (Router $router) {
	$router->addRoute("/", "HomeController@index");
	$router->addRoute("/data/{data}", "HomeController@index");
	$router->addRoute("/about", "AboutController@show");
	$router->addRoute("/contact", "ContactController@store", "POST");

	$router->addRoute("/test", function ($req, $res) {
		$res->body()->add(" world!")->prepend('Hello,');
	});
});
