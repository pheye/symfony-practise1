<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$routes = new RouteCollection();

$routes->add('hello', new Route('/hello/{name}', [
    'name' => 'World',
    '_controller' => function($request) {
        $request->attributes->set('foo', 'bar');
        $response = render_template($request);

        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
]));

$routes->add('leap_year', new Route('/is_leap_year/{year}', [
    'year' => null,
    '_controller' => 'Calendar\Controller\LeapYearController::indexAction'
]));

return $routes;
