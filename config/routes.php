<?php
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::defaultRouteClass(DashedRoute::class);

/* 
Router::scope( 
	"the url to start with", 
	"the controller to connect to", 
	"a function that connects the 'tagged' url with * parameters to the 'tags' function in the controller" )
*/

Router::scope(
	'/bookmarks',
	['controller' => 'Bookmarks'],
	function($routes) {
		$routes->connect('/tagged/*', ['action' => 'tags']);
	}
);

Router::scope(
	'/', 
	function ($routes) {
    	$routes->connect('/', ['controller' => 'Bookmarks', 'action' => 'index', 'home']);
    	$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
    	$routes->fallbacks(DashedRoute::class);
	}
);

//Plugin::routes();
