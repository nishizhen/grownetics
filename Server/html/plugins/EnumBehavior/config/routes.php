<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'EnumBehavior',
    ['path' => '/enum-behavior'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
