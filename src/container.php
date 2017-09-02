<?php
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\EventDispatcher;
use Simplex\Framework;
use Simplex\StringResponseListener;


$sc = new DependencyInjection\ContainerBuilder();
$sc->setParameter('charset', 'utf-8');

$sc->register('context', Routing\RequestContext::class);
$sc->register('matcher',  Routing\Matcher\UrlMatcher::class)
	->setArguments(['%routes%', new Reference('context')]);

$sc->register('request_stack', HttpFoundation\RequestStack::class);
$sc->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
$sc->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class);

$sc->register('listener.router', HttpKernel\EventListener\RouterListener::class)
    ->setArguments(array(new Reference('matcher'), new Reference('request_stack')));

$sc->register('listener.response', HttpKernel\EventListener\ResponseListener::class)
    ->setArguments(array('%charset%'))
;

$sc->register('listener.exception', HttpKernel\EventListener\ExceptionListener::class)
    ->setArguments(array('Calendar\Controller\ErrorController::exceptionAction'))
;

$sc->register('listener.string_response', StringResponseListener::class);
$sc->register('dispatcher', EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

$sc->getDefinition('dispatcher')->addMethodCall('addSubscriber', [new Reference('listener.string_response')]);

$sc->register('framework', Framework::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver')
    ]);

return $sc;


