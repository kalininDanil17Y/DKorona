<?php
require_once __DIR__ . '/../my_autoload.php';

use DKorona\DKCore;
use DKorona\Router;

$core = new DKCore();

$core->setupRouters(function (Router $router) {
	$router->addRoute("/", "HomeController@index")->name('home');

	$router->addRoute("/data/", "HomeController@user");
	$router->addRoute("/data/{id}", "HomeController@user")->name('get_user');

	$router->group('/about', function (Router $router) {
		$router->addRoute("", "AboutController@show");
		$router->addRoute("/2", "AboutController@no_method")->name('.2');
	}, 'about');

	$router->addRoute("/contact", "ContactController@store", "POST");

	$router->addRoute("/admin", "ContactController@admin", "GET")->middleware('AuthMiddleware');

	$router->addRoute("/test", function ($req, $res) {
		$res->body()->add(" world!")->prepend('Hello,');
	});
});
