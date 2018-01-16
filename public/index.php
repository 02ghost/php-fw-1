<?php

require __DIR__ . '/../vendor/autoload.php';

use Metinet\Core\Http\Request;
use Metinet\Core\Http\Response;
use Metinet\Core\Routing\Route;
use Metinet\Core\Routing\RouteUrlMatcher;
use Metinet\Core\Routing\RouteNotFound;
use Metinet\Core\Routing\JsonFileLoader;


function retrieveMemberList(): Response {
    $members = [
        ['name' => 'Boris', 'birthday' => '1984-08-21']
    ];
    $content = sprintf('<p>Affiche la liste des membres</p>');
    foreach ($members as $member) {
        $content .= sprintf('<li>%s</li>', $member['name']);
    }

    return new Response($content);
}

function sayHello(string $name = 'Anonymous'): Response {
    throw new Exception('Je ne dis pas bonjour aux Anonymous');
    return new Response(sprintf('<p>Hello %s</p>', $name));
}

function throwError($message): Response {
    return new Response(sprintf('<p>Error: %s</p>', $message), 500);
}

$request = Request::createFromGlobals();

$loader = new JsonFileLoader([__DIR__ . '/../conf/routing.json']);

$routeUrlMatcher = new RouteUrlMatcher($loader->load());

try {
    $action = $routeUrlMatcher->match($request);
    $response = call_user_func($action);
} catch (RouteNotFound $e) {
    $response = new Response('Aucune page ici', 404);
} catch (Exception $e) {
    $response = throwError($e->getMessage());
}
$response->send();
