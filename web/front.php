<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

/* $dispatcher->addListener('response', [new Simplex\GoogleListener(), 'onResponse']); */

$sc = include __DIR__.'/../src/container.php';
$sc->setParameter('routes', include __DIR__ . '/../src/app.php');
$request = Request::createFromGlobals();
$response = $sc->get('framework')->handle($request);
$response->send();
