<?php
require_once __DIR__ . '/../my_autoload.php';

use DKLittleSite\DKCore;
use DKLittleSite\Router;

$core = new DKCore();

$core->setupRouters(function (Router $router) {
	$router->addRoute("/", "HomeController@index")->name('home');

	$router->addRoute("/data/", "HomeController@user");
	$router->addRoute("/data/{id}", "HomeController@user")->name('get_user');

	$router->addRoute("/about", "AboutController@show")->name('about');
	$router->addRoute("/about/2", "AboutController@no_method");

	$router->addRoute("/contact", "ContactController@store", "POST");

	$router->addRoute("/test", function ($req, $res) {
		$res->body()->add(" world!")->prepend('Hello,');
	});
});
